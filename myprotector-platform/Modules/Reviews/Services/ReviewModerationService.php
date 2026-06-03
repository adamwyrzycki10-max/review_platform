<?php
/**
 * MyProtector Platform - Review Moderation Service
 * 
 * Handles review moderation business logic
 * 
 * @package MyProtector\Modules\Reviews\Services
 * @version 1.0.0
 */

namespace MyProtector\Modules\Reviews\Services;

class ReviewModerationService {
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