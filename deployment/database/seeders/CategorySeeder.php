<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Tech-related domains including software, apps, and digital services',
                'icon' => 'fas fa-laptop-code',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business and corporate domains for companies and enterprises',
                'icon' => 'fas fa-briefcase',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Short Names',
                'slug' => 'short-names',
                'description' => 'Short, memorable domain names perfect for branding',
                'icon' => 'fas fa-text-width',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'High-value premium domains with exceptional potential',
                'icon' => 'fas fa-crown',
                'color' => '#8B5CF6',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Domains perfect for online stores and e-commerce platforms',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Financial services, banking, and investment domains',
                'icon' => 'fas fa-chart-line',
                'color' => '#06B6D4',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Health & Fitness',
                'slug' => 'health-fitness',
                'description' => 'Health, wellness, and fitness-related domains',
                'icon' => 'fas fa-heartbeat',
                'color' => '#EC4899',
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational institutions, courses, and learning platforms',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#84CC16',
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'description' => 'Entertainment, media, and creative industry domains',
                'icon' => 'fas fa-film',
                'color' => '#F97316',
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'name' => 'Real Estate',
                'slug' => 'real-estate',
                'description' => 'Real estate, property, and housing-related domains',
                'icon' => 'fas fa-home',
                'color' => '#6B7280',
                'is_active' => true,
                'sort_order' => 10
            ]
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}