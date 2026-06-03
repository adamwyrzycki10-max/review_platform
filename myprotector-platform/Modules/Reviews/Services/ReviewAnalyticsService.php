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

use MyProtector\Core\Services\Container\ServiceContainer;

class ReviewAnalyticsService {
    /**
     * Service container
     * 
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param ServiceContainer $container
     */
    public function __construct(ServiceContainer $container) {
        $this->container = $container;
    }

    /**
     * Get container
     * 
     * @return ServiceContainer
     */
    public function getContainer(): ServiceContainer {
        return $this->container;
    }
}