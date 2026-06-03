<?php
/**
 * MyProtector Platform - Review Analytics Service
 * 
 * Handles review analytics business logic
 * 
 * @package MyProtector\Modules\Reviews\Services
 * @version 1.0.0
 */

namespace MyProtector\Modules\Reviews\Services;

class ReviewAnalyticsService {
    /**
     * Service container
     * 
     * @var array
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param array $container
     */
    public function __construct($container = []) {
        $this->container = $container;
    }

    /**
     * Get container
     * 
     * @return array
     */
    public function getContainer(): array {
        return $this->container;
    }
}