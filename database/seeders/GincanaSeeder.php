<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gincana;
use App\Models\GincanaLocal;

class GincanaSeeder extends Seeder
{
    public function run()
    {
        // Criar gincanas públicas com locais interessantes do Brasil
        $gincanas = [
            [
                'nome' => 'Pontos Turísticos do Rio de Janeiro',
                'duracao' => 30,
                'latitude' => -22.9068,
                'longitude' => -43.1729,
                'contexto' => 'Explore os principais pontos turísticos do Rio de Janeiro',
                'privacidade' => 'publica',
                'locais' => [
                    ['lat' => -22.9068, 'lng' => -43.1729], // Cristo Redentor
                    ['lat' => -22.9519, 'lng' => -43.2105], // Copacabana
                    ['lat' => -22.9083, 'lng' => -43.1964], // Pão de Açúcar
                    ['lat' => -22.9461, 'lng' => -43.1811], // Ipanema
                ]
            ],
            [
                'nome' => 'Capitais do Brasil',
                'duracao' => 45,
                'latitude' => -23.5505,
                'longitude' => -46.6333,
                'contexto' => 'Conheça as principais capitais do Brasil',
                'privacidade' => 'publica',
                'locais' => [
                    ['lat' => -23.5505, 'lng' => -46.6333], // São Paulo
                    ['lat' => -15.7942, 'lng' => -47.8822], // Brasília
                    ['lat' => -12.9714, 'lng' => -38.5014], // Salvador
                    ['lat' => -25.4284, 'lng' => -49.2733], // Curitiba
                    ['lat' => -30.0346, 'lng' => -51.2177], // Porto Alegre
                ]
            ],
            [
                'nome' => 'Nordeste Brasileiro',
                'duracao' => 35,
                'latitude' => -8.0476,
                'longitude' => -34.8770,
                'contexto' => 'Descubra as belezas do Nordeste',
                'privacidade' => 'publica',
                'locais' => [
                    ['lat' => -8.0476, 'lng' => -34.8770], // Recife
                    ['lat' => -3.7192, 'lng' => -38.5267], // Fortaleza
                    ['lat' => -9.6658, 'lng' => -35.7353], // Maceió
                    ['lat' => -5.7945, 'lng' => -35.2110], // Natal
                ]
            ],
            [
                'nome' => 'Centro-Oeste em Foco',
                'duracao' => 40,
                'latitude' => -15.7942,
                'longitude' => -47.8822,
                'contexto' => 'Explore o centro do Brasil',
                'privacidade' => 'publica',
                'locais' => [
                    ['lat' => -15.7942, 'lng' => -47.8822], // Brasília
                    ['lat' => -15.6014, 'lng' => -56.0979], // Cuiabá
                    ['lat' => -20.4428, 'lng' => -54.6464], // Campo Grande
                    ['lat' => -10.2491, 'lng' => -48.3243], // Palmas
                ]
            ],
            [
                'nome' => 'Sul Maravilhoso',
                'duracao' => 30,
                'latitude' => -27.5954,
                'longitude' => -48.5480,
                'contexto' => 'Conheça o Sul do Brasil',
                'privacidade' => 'publica',
                'locais' => [
                    ['lat' => -27.5954, 'lng' => -48.5480], // Florianópolis
                    ['lat' => -25.4284, 'lng' => -49.2733], // Curitiba
                    ['lat' => -30.0346, 'lng' => -51.2177], // Porto Alegre
                    ['lat' => -26.9194, 'lng' => -49.0661], // Blumenau
                ]
            ]
        ];

        foreach ($gincanas as $gincanaData) {
            $locais = $gincanaData['locais'];
            unset($gincanaData['locais']);
            
            $gincana = Gincana::create($gincanaData);
            
            foreach ($locais as $local) {
                GincanaLocal::create([
                    'gincana_id' => $gincana->id,
                    'latitude' => $local['lat'],
                    'longitude' => $local['lng']
                ]);
            }
        }
    }
}
