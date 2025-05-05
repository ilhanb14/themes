<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssertTextTest extends TestCase
{
    /**
     * Test that specific text appears on a page.
     */
    public function test_page_contains_text(): void
    {
        // Visit the homepage
        $response = $this->get('/');
        
        // Assert the response status is 200 (OK)
        $response->assertStatus(200);
        
        // Assert that the page contains specific text that's actually on the page
        $response->assertSee('De Blauwe Vogel Themadagen');
        
        // Check for HTML elements that should exist
        $response->assertSee('<header', false);
        
        // Check for the page title
        $response->assertSee('<title>De Blauwe Vogel Themadagen - Unieke steden in de wereld</title>', false);
        
        // Check text doesn't exist (this should pass if this text isn't on your page)
        $response->assertDontSee('error');
    }
}