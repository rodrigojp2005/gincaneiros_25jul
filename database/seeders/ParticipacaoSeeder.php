<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Gincana;
use App\Models\Participacao;
use Carbon\Carbon;

class ParticipacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primeiro, vamos criar alguns usuários se não existirem
        $usuarios = [];
        $nomesUsuarios = [
            'Ana Silva',
            'Carlos Santos',
            'Beatriz Lima',
            'Diego Oliveira',
            'Eduarda Costa',
            'Felipe Rodriguez',
            'Gabriela Martins',
            'Henrique Pereira',
            'Isabela Ferreira',
            'João Almeida'
        ];

        foreach ($nomesUsuarios as $nome) {
            $email = strtolower(str_replace(' ', '.', $nome)) . '@exemplo.com';
            $usuario = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nome,
                    'password' => bcrypt('password123')
                ]
            );
            $usuarios[] = $usuario;
        }

        // Buscar gincanas existentes
        $gincanas = Gincana::all();
        
        if ($gincanas->count() == 0) {
            $this->command->info('Nenhuma gincana encontrada. Criando algumas para teste...');
            
            // Criar algumas gincanas de exemplo
            $gincanasExemplo = [
                [
                    'nome' => 'Caça ao Tesouro do Centro',
                    'duracao' => 60,
                    'latitude' => -22.9068,
                    'longitude' => -43.1729,
                    'contexto' => 'Explore os pontos históricos do centro da cidade',
                    'privacidade' => 'publica'
                ],
                [
                    'nome' => 'Aventura na Praia',
                    'duracao' => 90,
                    'latitude' => -22.9711,
                    'longitude' => -43.1823,
                    'contexto' => 'Descubra os segredos escondidos da orla',
                    'privacidade' => 'publica'
                ],
                [
                    'nome' => 'Mistério do Parque',
                    'duracao' => 45,
                    'latitude' => -22.9483,
                    'longitude' => -43.1765,
                    'contexto' => 'Resolva enigmas no maior parque da cidade',
                    'privacidade' => 'publica'
                ]
            ];

            foreach ($gincanasExemplo as $gincanaData) {
                $gincanas[] = Gincana::create($gincanaData);
            }
            
            $gincanas = collect($gincanas);
        }

        $this->command->info('Criando participações de exemplo...');

        // Criar participações para cada gincana
        foreach ($gincanas as $gincana) {
            // Número aleatório de participantes (entre 3 e 8)
            $numParticipantes = rand(3, 8);
            $usuariosParticipantes = collect($usuarios)->random($numParticipantes);

            foreach ($usuariosParticipantes as $index => $usuario) {
                // Gerar dados aleatórios mas realistas
                $inicioParticipacao = Carbon::now()->subDays(rand(1, 30))->subHours(rand(1, 12));
                $tempoTotalSegundos = rand(1800, $gincana->duracao * 60 + 600); // Entre 30min e duração + 10min
                $fimParticipacao = $inicioParticipacao->copy()->addSeconds($tempoTotalSegundos);
                
                // Pontuação baseada na posição e fatores aleatórios
                $pontuacaoBase = 1000;
                $bonusVelocidade = max(0, ($gincana->duracao * 60 - $tempoTotalSegundos) * 2);
                $pontuacaoAleatoria = rand(-100, 200);
                $pontuacao = $pontuacaoBase + $bonusVelocidade + $pontuacaoAleatoria;
                
                // Garantir que a pontuação seja positiva
                $pontuacao = max(100, $pontuacao);
                
                // Locais visitados (normalmente relacionado à pontuação)
                $locaisVisitados = rand(3, 8);
                
                Participacao::create([
                    'user_id' => $usuario->id,
                    'gincana_id' => $gincana->id,
                    'pontuacao' => $pontuacao,
                    'inicio_participacao' => $inicioParticipacao,
                    'fim_participacao' => $fimParticipacao,
                    'tempo_total_segundos' => $tempoTotalSegundos,
                    'status' => 'concluida',
                    'locais_visitados' => $locaisVisitados
                ]);
            }
            
            $this->command->info("Criadas {$numParticipantes} participações para a gincana: {$gincana->nome}");
        }

        // Criar algumas participações em andamento
        $usuariosEmAndamento = collect($usuarios)->random(3);
        $gincanasAleatorias = $gincanas->random(2);
        
        foreach ($usuariosEmAndamento as $usuario) {
            foreach ($gincanasAleatorias as $gincana) {
                // Verificar se já não tem participação nesta gincana
                $jaParticipa = Participacao::where('user_id', $usuario->id)
                    ->where('gincana_id', $gincana->id)
                    ->exists();
                    
                if (!$jaParticipa) {
                    Participacao::create([
                        'user_id' => $usuario->id,
                        'gincana_id' => $gincana->id,
                        'pontuacao' => rand(0, 500),
                        'inicio_participacao' => Carbon::now()->subMinutes(rand(10, 120)),
                        'fim_participacao' => null,
                        'tempo_total_segundos' => null,
                        'status' => 'em_andamento',
                        'locais_visitados' => rand(1, 4)
                    ]);
                }
            }
        }

        $this->command->info('Participações de exemplo criadas com sucesso!');
        $this->command->info('Total de participações: ' . Participacao::count());
        $this->command->info('Participações concluídas: ' . Participacao::where('status', 'concluida')->count());
        $this->command->info('Participações em andamento: ' . Participacao::where('status', 'em_andamento')->count());
    }
}
