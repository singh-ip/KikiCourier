<?php

declare(strict_types=1);

namespace KikiCourier\Application;
use KikiCourier\Parser\InputParser;
use KikiCourier\Parser\OutputFormatter;
use KikiCourier\Parser\VehicleParser;
use KikiCourier\Parser\DeliveryOutputFormatter;
use KikiCourier\Service\DeliveryCostCalculator;
use KikiCourier\Service\OfferRegistry;
use KikiCourier\Service\OfferService;
use KikiCourier\Service\ShipmentPlanner;
use KikiCourier\Service\DeliveryScheduler;
use KikiCourier\Entity\Vehicle;
use KikiCourier\Offer\OfferFactory;

final class CourierServiceApp
{
    private InputParser $parser;
    private VehicleParser $vehicleParser;
    private DeliveryOutputFormatter $formatter;
    private OfferRegistry $offerRegistry;
    const OFFER_CONFIG_FILE = __DIR__ . '/../config/offer.json';

    /** @var resource */
    private $inputHandle;

    /** @var resource */
    private $outputHandle;
    
    public function __construct(
        $inputHandle,
        $outputHandle
    ) {
        $this->inputHandle = $inputHandle;
        $this->outputHandle = $outputHandle;
        $this->parser = new InputParser();
        $this->vehicleParser = new VehicleParser();
        $this->formatter = new DeliveryOutputFormatter();
        $this->offerRegistry = $this->initializeOffers();
    }

    private function readLine(): string
    {
        $line = fgets($this->inputHandle);
        return $line !== false ? trim($line) : '';
    }

    private function writeLine(string $line): void
    {
        fwrite($this->outputHandle, $line . PHP_EOL);
    }

    private function initializeOffers(): OfferRegistry
    {
        $registry = new OfferRegistry();

        $offers = $this->getOffers();
        foreach ($offers as $offer) {
            $registry->register($offer);
        }

        return $registry;
    }

    private function getOffers(): array
    {
        if (file_exists(self::OFFER_CONFIG_FILE)) {
            return OfferFactory::loadFromJson(self::OFFER_CONFIG_FILE);
        }

        return OfferFactory::createStandardOffers();
    }

    public function run(): void
    {
        $firstLine = $this->readLine();
        $baseData = $this->parser->parseBaseAndCount($firstLine);
        
        $calculator = new DeliveryCostCalculator($baseData['base_cost']);
        $offerService = new OfferService($this->offerRegistry);
        
        $packageCount = $baseData['package_count'];
        $packages = [];
        
        for ($i = 0; $i < $packageCount; $i++) {
            $line = $this->readLine();
            $package = $this->parser->parsePackage($line);
            
            $deliveryCost = $calculator->calculateDeliveryCost($package);
            $discount = $offerService->applyOffer($package, $deliveryCost);
            $package->setDiscount($discount);

            $totalCost = $calculator->calculateTotalCostWithDiscount($package, $deliveryCost);
            $package->setTotalCost($totalCost);
            
            $packages[] = $package;
        }
        
        $vehicleLine = $this->readLine();
        
        if (empty($vehicleLine)) {
            $this->outputCostOnly($packages);
            return;
        }
        
        $this->runWithDeliveryTime($packages, $vehicleLine);
    }

    private function runWithDeliveryTime(array $packages, string $vehicleLine): void
    {
        $vehicleConfig = $this->vehicleParser->parseVehicleConfig($vehicleLine);
        
        $vehicles = [];
        for ($i = 0; $i < $vehicleConfig['count']; $i++) {
            $vehicles[] = new Vehicle(
                "V" . ($i + 1),
                $vehicleConfig['max_weight'],
                $vehicleConfig['speed']
            );
        }
        
        $planner = new ShipmentPlanner($vehicleConfig['max_weight']);
        $scheduler = new DeliveryScheduler($vehicles, $planner);
        $scheduler->scheduleDeliveries($packages);
        
        foreach ($packages as $package) {
            $this->writeLine($this->formatter->formatPackageWithTime($package));
        }
    }

    private function outputCostOnly(array $packages): void
    {
        $formatter = new OutputFormatter();
        foreach ($packages as $package) {
            $this->writeLine($formatter->formatPackageResult($package));
        }
    }
}