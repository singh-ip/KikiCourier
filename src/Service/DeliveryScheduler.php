<?php

declare(strict_types=1);

namespace KikiCourier\Service;

use KikiCourier\Entity\Vehicle;
use KikiCourier\Entity\Package;
use KikiCourier\Entity\Shipment;

final class DeliveryScheduler
{
	public function __construct(private array $vehicles, private ShipmentPlanner $planner)
	{
		if (empty($vehicles)) {
			throw new \InvalidArgumentException('At least one vehicle required');
		}
	}

	public function scheduleDeliveries(array $packages): void
	{
		$remainingPackages = $packages;

		while (!empty($remainingPackages)) {
			$vehicle = $this->findNextAvailableVehicle();
			$shipment = $this->planner->findBestShipment($remainingPackages);

			if ($shipment->isEmpty()) {
				break;
			}

			$this->assignShipment($vehicle, $shipment);
			$remainingPackages = $this->removeDeliveredPackages($remainingPackages, $shipment);
		}
	}

	private function findNextAvailableVehicle(): Vehicle
	{
		$earliest = $this->vehicles[0];

		foreach ($this->vehicles as $vehicle) {
			if ($vehicle->getAvailableAt() < $earliest->getAvailableAt()) {
				$earliest = $vehicle;
			}
		}

		return $earliest;
	}

	private function assignShipment(Vehicle $vehicle, Shipment $shipment): void
	{
		$departureHundredths = (int) round($vehicle->getAvailableAt() * 100);
		$speedKmPerHour = $vehicle->getSpeed()->getKmPerHour();

		$maxDistanceKm = $shipment->getMaxDistance();
		$maxTravelHundredths = $this->calculateTravelTimeInHundredths(
			$maxDistanceKm,
			$speedKmPerHour
		);

		foreach ($shipment->getPackages() as $package) {
			$packageDistanceKm = $package->getDistance()->getKilometers();
			$packageTravelHundredths = $this->calculateTravelTimeInHundredths(
				$packageDistanceKm,
				$speedKmPerHour
			);

			$deliveryHundredths = $departureHundredths + $packageTravelHundredths;
			$package->setEstimatedDeliveryTime($deliveryHundredths / 100);
		}

		$returnHundredths = $departureHundredths + (2 * $maxTravelHundredths);
		$vehicle->setAvailableAt($returnHundredths / 100);
	}

	private function calculateTravelTimeInHundredths(float $distanceKm, float $speedKmPerHour): int
	{
		if ($speedKmPerHour <= 0) {
			throw new \InvalidArgumentException('Speed must be positive');
		}

		$scaledDistance = (int) round($distanceKm * 100);
		$scaledSpeed = (int) round($speedKmPerHour);

		if ($scaledSpeed === 0) {
			throw new \InvalidArgumentException('Speed must be positive');
		}

		return intdiv($scaledDistance, $scaledSpeed);
	}

	private function removeDeliveredPackages(array $packages, Shipment $shipment): array
	{
		$deliveredIds = [];
		foreach ($shipment->getPackages() as $package) {
			$deliveredIds[] = $package->getId()->getId();
		}

		$remaining = array_filter(
			$packages,
			function ($package) use ($deliveredIds) {
				return !in_array($package->getId()->getId(), $deliveredIds, true);
			}
		);

		return array_values($remaining);
	}
}