<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'   => 'Guide complet SYSCOHADA pour les PME camerounaises',
                'excerpt' => 'Comprendre le plan comptable OHADA révisé : classes de comptes, journaux, et bonnes pratiques pour tenir une comptabilité conforme au Cameroun.',
                'body'    => "## Le plan comptable SYSCOHADA révisé\n\nLe référentiel SYSCOHADA structure la comptabilité en **9 classes de comptes** (1 à 9). Pour une PME, l'essentiel se joue dans les classes 6 (charges) et 7 (produits).\n\n### Les journaux essentiels\n\n- Journal des achats\n- Journal des ventes\n- Journal de banque et de caisse\n- Journal des opérations diverses\n\n### Bonnes pratiques\n\nSaisissez vos opérations au quotidien, rapprochez votre banque chaque mois, et préparez votre DSF en continu plutôt qu'en fin d'exercice. OPESBooks automatise la plupart de ces étapes.",
            ],
            [
                'title'   => 'Comment préparer votre DSF avec OPESBooks',
                'excerpt' => 'La Déclaration Statistique et Fiscale (DSF) est l\'obligation annuelle clé. Voici comment la préparer sans stress.',
                'body'    => "## Qu'est-ce que la DSF ?\n\nLa **DSF** est la déclaration annuelle que toute entreprise dépose auprès de la DGI. Elle synthétise vos comptes de l'exercice.\n\n### Étapes avec OPESBooks\n\n1. Tenez votre comptabilité à jour toute l'année.\n2. Lancez le **contrôle DSF par IA** pour repérer les anomalies.\n3. Générez l'export DSF / D10 prêt à transmettre.\n\nLe moniteur DGI vous indique les échéances et l'état de vos télétransmissions.",
            ],
            [
                'title'   => 'TVA au Cameroun : tout ce que vous devez savoir',
                'excerpt' => 'TVA à 17,5%, CAC à 10% de la TVA, soit 19,25% TTC. Décryptage du calcul et des obligations.',
                'body'    => "## Le taux effectif de 19,25%\n\nAu Cameroun, la **TVA** est de 17,5%, à laquelle s'ajoute le **Centime Additionnel Communal (CAC)** égal à 10% de la TVA — soit 1,75% du HT. Le taux effectif est donc de **19,25%**.\n\n### Exemple\n\nPour 1 000 000 XAF HT :\n- TVA (17,5%) : 175 000 XAF\n- CAC (10% de la TVA) : 17 500 XAF\n- **TTC : 1 192 500 XAF**\n\nOPESBooks calcule tout automatiquement sur vos factures et écritures.",
            ],
        ];

        foreach ($posts as $i => $p) {
            BlogPost::updateOrCreate(['slug' => Str::slug($p['title'])], [
                'title'                => $p['title'],
                'excerpt'              => $p['excerpt'],
                'body'                 => $p['body'],
                'is_published'         => true,
                'published_at'         => now()->subDays(($i + 1) * 3),
                'reading_time_minutes' => max(2, (int) ceil(str_word_count($p['body']) / 200)),
                'tags'                 => ['SYSCOHADA', 'DGI', 'Cameroun'],
                'meta_description'     => $p['excerpt'],
            ]);
        }
    }
}
