<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Commune;
use Illuminate\Database\Seeder;

class ChileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['id' => 15, 'name' => 'Arica y Parinacota', 'ordinal' => 'XV'],
            ['id' => 1, 'name' => 'Tarapacá', 'ordinal' => 'I'],
            ['id' => 2, 'name' => 'Antofagasta', 'ordinal' => 'II'],
            ['id' => 3, 'name' => 'Atacama', 'ordinal' => 'III'],
            ['id' => 4, 'name' => 'Coquimbo', 'ordinal' => 'IV'],
            ['id' => 5, 'name' => 'Valparaíso', 'ordinal' => 'V'],
            ['id' => 13, 'name' => 'Metropolitana de Santiago', 'ordinal' => 'RM'],
            ['id' => 6, 'name' => 'Libertador General Bernardo O\'Higgins', 'ordinal' => 'VI'],
            ['id' => 7, 'name' => 'Maule', 'ordinal' => 'VII'],
            ['id' => 16, 'name' => 'Ñuble', 'ordinal' => 'XVI'],
            ['id' => 8, 'name' => 'Biobío', 'ordinal' => 'VIII'],
            ['id' => 9, 'name' => 'Araucanía', 'ordinal' => 'IX'],
            ['id' => 14, 'name' => 'Los Ríos', 'ordinal' => 'XIV'],
            ['id' => 10, 'name' => 'Los Lagos', 'ordinal' => 'X'],
            ['id' => 11, 'name' => 'Aysén del Gral. Carlos Ibáñez del Campo', 'ordinal' => 'XI'],
            ['id' => 12, 'name' => 'Magallanes y de la Antártica Chilena', 'ordinal' => 'XII'],
        ];

        foreach ($regions as $r) {
            Region::updateOrCreate(['id' => $r['id']], [
                'name' => $r['name'],
                'ordinal' => $r['ordinal']
            ]);
        }

        $communes = [
            // Arica y Parinacota
            ['id' => 1, 'region_id' => 15, 'name' => 'Arica'],
            ['id' => 2, 'region_id' => 15, 'name' => 'Camarones'],
            // Tarapacá
            ['id' => 3, 'region_id' => 1, 'name' => 'Iquique'],
            ['id' => 4, 'region_id' => 1, 'name' => 'Alto Hospicio'],
            // Antofagasta
            ['id' => 5, 'region_id' => 2, 'name' => 'Antofagasta'],
            ['id' => 6, 'region_id' => 2, 'name' => 'Calama'],
            ['id' => 7, 'region_id' => 2, 'name' => 'Tocopilla'],
            // Atacama
            ['id' => 8, 'region_id' => 3, 'name' => 'Copiapó'],
            ['id' => 9, 'region_id' => 3, 'name' => 'Vallenar'],
            // Coquimbo
            ['id' => 10, 'region_id' => 4, 'name' => 'La Serena'],
            ['id' => 11, 'region_id' => 4, 'name' => 'Coquimbo'],
            ['id' => 12, 'region_id' => 4, 'name' => 'Ovalle'],
            // Valparaíso
            ['id' => 13, 'region_id' => 5, 'name' => 'Valparaíso'],
            ['id' => 14, 'region_id' => 5, 'name' => 'Viña del Mar'],
            ['id' => 15, 'region_id' => 5, 'name' => 'Concón'],
            ['id' => 16, 'region_id' => 5, 'name' => 'Quilpué'],
            ['id' => 17, 'region_id' => 5, 'name' => 'Villa Alemana'],
            ['id' => 18, 'region_id' => 5, 'name' => 'San Antonio'],
            // Metropolitana de Santiago
            ['id' => 19, 'region_id' => 13, 'name' => 'Santiago'],
            ['id' => 20, 'region_id' => 13, 'name' => 'Providencia'],
            ['id' => 21, 'region_id' => 13, 'name' => 'Las Condes'],
            ['id' => 22, 'region_id' => 13, 'name' => 'Vitacura'],
            ['id' => 23, 'region_id' => 13, 'name' => 'Lo Barnechea'],
            ['id' => 24, 'region_id' => 13, 'name' => 'Ñuñoa'],
            ['id' => 25, 'region_id' => 13, 'name' => 'La Reina'],
            ['id' => 26, 'region_id' => 13, 'name' => 'Macul'],
            ['id' => 27, 'region_id' => 13, 'name' => 'San Miguel'],
            ['id' => 28, 'region_id' => 13, 'name' => 'Estación Central'],
            ['id' => 29, 'region_id' => 13, 'name' => 'Maipú'],
            ['id' => 30, 'region_id' => 13, 'name' => 'La Florida'],
            ['id' => 31, 'region_id' => 13, 'name' => 'Puente Alto'],
            ['id' => 32, 'region_id' => 13, 'name' => 'San Bernardo'],
            ['id' => 33, 'region_id' => 13, 'name' => 'Colina'],
            ['id' => 34, 'region_id' => 13, 'name' => 'Lampa'],
            // O'Higgins
            ['id' => 35, 'region_id' => 6, 'name' => 'Rancagua'],
            ['id' => 36, 'region_id' => 6, 'name' => 'San Fernando'],
            // Maule
            ['id' => 37, 'region_id' => 7, 'name' => 'Talca'],
            ['id' => 38, 'region_id' => 7, 'name' => 'Curicó'],
            ['id' => 39, 'region_id' => 7, 'name' => 'Linares'],
            // Ñuble
            ['id' => 40, 'region_id' => 16, 'name' => 'Chillán'],
            ['id' => 41, 'region_id' => 16, 'name' => 'San Carlos'],
            // Biobío
            ['id' => 42, 'region_id' => 8, 'name' => 'Concepción'],
            ['id' => 43, 'region_id' => 8, 'name' => 'Talcahuano'],
            ['id' => 44, 'region_id' => 8, 'name' => 'San Pedro de la Paz'],
            ['id' => 45, 'region_id' => 8, 'name' => 'Chiguayante'],
            ['id' => 46, 'region_id' => 8, 'name' => 'Los Ángeles'],
            // Araucanía
            ['id' => 47, 'region_id' => 9, 'name' => 'Temuco'],
            ['id' => 48, 'region_id' => 9, 'name' => 'Padre Las Casas'],
            ['id' => 49, 'region_id' => 9, 'name' => 'Villarrica'],
            // Los Ríos
            ['id' => 50, 'region_id' => 14, 'name' => 'Valdivia'],
            ['id' => 51, 'region_id' => 14, 'name' => 'La Unión'],
            // Los Lagos
            ['id' => 52, 'region_id' => 10, 'name' => 'Puerto Montt'],
            ['id' => 53, 'region_id' => 10, 'name' => 'Osorno'],
            ['id' => 54, 'region_id' => 10, 'name' => 'Castro'],
            // Aysén
            ['id' => 55, 'region_id' => 11, 'name' => 'Coyhaique'],
            // Magallanes
            ['id' => 56, 'region_id' => 12, 'name' => 'Punta Arenas'],
        ];

        foreach ($communes as $c) {
            Commune::updateOrCreate(['id' => $c['id']], [
                'region_id' => $c['region_id'],
                'name' => $c['name']
            ]);
        }
    }
}
