<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

final class InitialCatalogImporter
{
    public function __construct(private NuttimeProductMedia $productMedia) {}

    /** @return array{created: int, translated: int} */
    public function import(): array
    {
        return DB::transaction(function (): array {
            $created = 0;
            $translated = 0;

            foreach ($this->products() as $sortOrder => $definition) {
                $product = Product::query()->firstOrCreate(
                    ['slug' => $definition['slug']],
                    [
                        'name' => $definition['translations']['tr']['name'],
                        'short_description' => $definition['translations']['tr']['short_description'],
                        'description' => $definition['translations']['tr']['description'],
                        'main_image' => $definition['images'][0] ?? null,
                        'additional_images' => array_slice($definition['images'], 1),
                        'nutrition_facts' => $definition['nutrition_facts'],
                        'packaging_details' => $this->standardPackagingDetails(),
                        'weight_grams' => 250,
                        'primary_ingredient_percentage' => $definition['percentage'],
                        'is_active' => true,
                        'is_featured' => false,
                        'sort_order' => $sortOrder,
                    ],
                );

                $created += $product->wasRecentlyCreated ? 1 : 0;

                foreach ($definition['translations'] as $locale => $translation) {
                    $translation['meta_title'] = $translation['name'].' | Nuttime';
                    $translation['meta_description'] = $translation['short_description'];

                    $wasMissing = ! $product->translations()->where('locale', $locale)->exists();
                    $product->translations()->firstOrCreate(['locale' => $locale], $translation);
                    $translated += $wasMissing ? 1 : 0;
                }
            }

            return compact('created', 'translated');
        });
    }

    /** @return array<int, array<string, mixed>> */
    private function products(): array
    {
        return [
            $this->product('findik-kremasi', 45, 'hazelnut', ['544 / 2277', '33,4', '5,15', '5,15', '46,8', '46', '5,05', '11,6', '0,122']),
            $this->product('antep-fistikli-kremasi', 42, 'pistachio', ['560 / 2344', '37', '7,85', '7,85', '40', '39,3', '5,48', '14,1', '0,065']),
            $this->product('badem-ezmesi', 45, 'almond', ['561 / 2348', '38,2', '6,56', '6,56', '36', '34,5', '7,45', '14,5', '0,112']),
            $this->product('yer-fistigi-ezmesi', 52, 'peanut', ['556 / 2337', '36', '8,39', '8,39', '39,3', '34,6', '5,16', '16,2', '0,077']),
            $this->product('seker-ilavesiz-yer-fistigi-ezmesi', 72, 'peanut', ['570 / 2306', '37,7', '8,21', '8,21', '29,5', '17,3', '7,69', '22,9', '0,101']),
            $this->product('hindistan-cevizi-ezmesi', 42, 'coconut', ['567 / 2773', '38,8', '28,6', '28,6', '43,6', '39,3', '6,82', '7,43', '0,094']),
        ];
    }

    /** @param array<int, string> $nutritionValues @return array<string, mixed> */
    private function product(string $key, int $percentage, string $mediaKey, array $nutritionValues): array
    {
        $names = [
            'findik-kremasi' => ['tr' => 'Fındık Ezmesi', 'en' => 'Hazelnut Butter', 'de' => 'Haselnusscreme', 'fr' => 'Pâte de noisettes', 'es' => 'Crema de avellanas', 'it' => 'Crema di nocciole', 'ru' => 'Фундучная паста', 'ar' => 'كريمة البندق', 'zh' => '榛子酱', 'pt' => 'Creme de avelã'],
            'antep-fistikli-kremasi' => ['tr' => 'Antep Fıstığı Ezmesi', 'en' => 'Pistachio Butter', 'de' => 'Pistaziencreme', 'fr' => 'Pâte de pistache', 'es' => 'Crema de pistacho', 'it' => 'Crema di pistacchio', 'ru' => 'Фисташковая паста', 'ar' => 'كريمة الفستق', 'zh' => '开心果酱', 'pt' => 'Creme de pistácio'],
            'badem-ezmesi' => ['tr' => 'Badem Ezmesi', 'en' => 'Almond Butter', 'de' => 'Mandelcreme', 'fr' => 'Pâte d’amandes', 'es' => 'Crema de almendras', 'it' => 'Crema di mandorle', 'ru' => 'Миндальная паста', 'ar' => 'كريمة اللوز', 'zh' => '杏仁酱', 'pt' => 'Creme de amêndoa'],
            'yer-fistigi-ezmesi' => ['tr' => 'Yer Fıstığı Ezmesi', 'en' => 'Peanut Butter', 'de' => 'Erdnusscreme', 'fr' => 'Beurre de cacahuète', 'es' => 'Crema de cacahuete', 'it' => 'Burro di arachidi', 'ru' => 'Арахисовая паста', 'ar' => 'زبدة الفول السوداني', 'zh' => '花生酱', 'pt' => 'Pasta de amendoim'],
            'seker-ilavesiz-yer-fistigi-ezmesi' => ['tr' => 'Şeker İlavesiz Yer Fıstığı Ezmesi', 'en' => 'No Added Sugar Peanut Butter', 'de' => 'Erdnusscreme ohne Zuckerzusatz', 'fr' => 'Beurre de cacahuète sans sucre ajouté', 'es' => 'Crema de cacahuete sin azúcar añadido', 'it' => 'Burro di arachidi senza zuccheri aggiunti', 'ru' => 'Арахисовая паста без добавления сахара', 'ar' => 'زبدة الفول السوداني من دون سكر مضاف', 'zh' => '无添加糖花生酱', 'pt' => 'Pasta de amendoim sem adição de açúcar'],
            'hindistan-cevizi-ezmesi' => ['tr' => 'Hindistan Cevizi Ezmesi', 'en' => 'Coconut Butter', 'de' => 'Kokoscreme', 'fr' => 'Crème de coco', 'es' => 'Crema de coco', 'it' => 'Crema di cocco', 'ru' => 'Кокосовая паста', 'ar' => 'كريمة جوز الهند', 'zh' => '椰子酱', 'pt' => 'Creme de coco'],
        ];
        $slugs = [
            'findik-kremasi' => ['tr' => 'findik-kremasi', 'en' => 'hazelnut-butter', 'de' => 'haselnusscreme', 'fr' => 'pate-de-noisettes', 'es' => 'crema-de-avellanas', 'it' => 'crema-di-nocciole', 'ru' => 'hazelnut-butter', 'ar' => 'hazelnut-butter', 'zh' => 'hazelnut-butter', 'pt' => 'creme-de-avela'],
            'antep-fistikli-kremasi' => ['tr' => 'antep-fistikli-kremasi', 'en' => 'pistachio-butter', 'de' => 'pistaziencreme', 'fr' => 'pate-de-pistache', 'es' => 'crema-de-pistacho', 'it' => 'crema-di-pistacchio', 'ru' => 'pistachio-butter', 'ar' => 'pistachio-butter', 'zh' => 'pistachio-butter', 'pt' => 'creme-de-pistacio'],
            'badem-ezmesi' => ['tr' => 'badem-ezmesi', 'en' => 'almond-butter', 'de' => 'mandelcreme', 'fr' => 'pate-d-amandes', 'es' => 'crema-de-almendras', 'it' => 'crema-di-mandorle', 'ru' => 'almond-butter', 'ar' => 'almond-butter', 'zh' => 'almond-butter', 'pt' => 'creme-de-amendoa'],
            'yer-fistigi-ezmesi' => ['tr' => 'yer-fistigi-ezmesi', 'en' => 'peanut-butter', 'de' => 'erdnusscreme', 'fr' => 'beurre-de-cacahuete', 'es' => 'crema-de-cacahuete', 'it' => 'burro-di-arachidi', 'ru' => 'peanut-butter', 'ar' => 'peanut-butter', 'zh' => 'peanut-butter', 'pt' => 'pasta-de-amendoim'],
            'seker-ilavesiz-yer-fistigi-ezmesi' => ['tr' => 'seker-ilavesiz-yer-fistigi-ezmesi', 'en' => 'no-added-sugar-peanut-butter', 'de' => 'erdnusscreme-ohne-zuckerzusatz', 'fr' => 'beurre-de-cacahuete-sans-sucre-ajoute', 'es' => 'crema-de-cacahuete-sin-azucar-anadido', 'it' => 'burro-di-arachidi-senza-zuccheri-aggiunti', 'ru' => 'no-added-sugar-peanut-butter', 'ar' => 'no-added-sugar-peanut-butter', 'zh' => 'no-added-sugar-peanut-butter', 'pt' => 'pasta-de-amendoim-sem-acucar-adicionado'],
            'hindistan-cevizi-ezmesi' => ['tr' => 'hindistan-cevizi-ezmesi', 'en' => 'coconut-butter', 'de' => 'kokoscreme', 'fr' => 'creme-de-coco', 'es' => 'crema-de-coco', 'it' => 'crema-di-cocco', 'ru' => 'coconut-butter', 'ar' => 'coconut-butter', 'zh' => 'coconut-butter', 'pt' => 'creme-de-coco'],
        ];
        $descriptions = $this->descriptions($key);
        $ingredients = $this->ingredients($key, $percentage);
        $allergens = $this->allergens($key);
        $images = $this->productMedia->galleryPaths($key === 'badem-ezmesi' ? 'badem-unu' : $key);

        return [
            'slug' => $key,
            'percentage' => $percentage,
            'images' => $images,
            'nutrition_facts' => array_combine(['energy', 'fat', 'saturated_fat', 'trans_fat', 'carbohydrates', 'sugar', 'fibre', 'protein', 'salt'], $nutritionValues),
            'translations' => collect(array_keys(config('nuttime.locales')))->mapWithKeys(fn (string $locale): array => [$locale => [
                'name' => $names[$key][$locale],
                'slug' => $slugs[$key][$locale],
                'short_description' => $descriptions[$locale],
                'description' => $descriptions[$locale],
                'ingredients' => $ingredients[$locale],
                'allergen_information' => $allergens[$locale],
            ]])->all(),
        ];
    }

    /** @return array<string, string> */
    private function descriptions(string $key): array
    {
        $descriptions = [
            'findik-kremasi' => ['tr' => 'Özenle seçilmiş fındıklarla hazırlanan pürüzsüz, yoğun ve dengeli lezzet.', 'en' => 'A smooth, rich and balanced spread made with carefully selected hazelnuts.', 'de' => 'Eine cremige, intensive und ausgewogene Spezialität aus sorgfältig ausgewählten Haselnüssen.', 'fr' => 'Une crème onctueuse, riche et équilibrée, préparée avec des noisettes soigneusement sélectionnées.', 'es' => 'Una crema suave, intensa y equilibrada elaborada con avellanas cuidadosamente seleccionadas.', 'it' => 'Una crema morbida, intensa ed equilibrata preparata con nocciole selezionate con cura.', 'ru' => 'Нежная, насыщенная и сбалансированная паста из тщательно отобранного фундука.', 'ar' => 'كريمة ناعمة وغنية ومتوازنة محضّرة من حبات بندق مختارة بعناية.', 'zh' => '以精心挑选的榛子制成，口感细腻、浓郁而平衡。', 'pt' => 'Um creme suave, intenso e equilibrado, preparado com avelãs cuidadosamente selecionadas.'],
            'antep-fistikli-kremasi' => ['tr' => 'Antep fıstığının karakteristik aromasıyla rafine bir deneyim.', 'en' => 'A refined experience with the distinctive aroma of pistachios.', 'de' => 'Ein raffiniertes Erlebnis mit dem unverwechselbaren Aroma von Pistazien.', 'fr' => 'Une expérience raffinée aux arômes caractéristiques de la pistache.', 'es' => 'Una experiencia refinada con el aroma característico del pistacho.', 'it' => 'Un’esperienza raffinata con l’aroma inconfondibile del pistacchio.', 'ru' => 'Изысканный вкус с характерным ароматом фисташек.', 'ar' => 'تجربة راقية بنكهة الفستق المميزة.', 'zh' => '呈现开心果独特香气的精致体验。', 'pt' => 'Uma experiência requintada com o aroma característico do pistácio.'],
            'badem-ezmesi' => ['tr' => 'Özenle seçilmiş bademlerin yumuşak ve karakterli lezzeti.', 'en' => 'The gentle, distinctive flavour of carefully selected almonds.', 'de' => 'Der milde, charaktervolle Geschmack sorgfältig ausgewählter Mandeln.', 'fr' => 'La saveur douce et affirmée d’amandes soigneusement sélectionnées.', 'es' => 'El sabor suave y distintivo de almendras cuidadosamente seleccionadas.', 'it' => 'Il gusto delicato e deciso di mandorle selezionate con cura.', 'ru' => 'Мягкий, выразительный вкус тщательно отобранного миндаля.', 'ar' => 'مذاق اللوز المختار بعناية، الناعم والغني بالشخصية.', 'zh' => '精心挑选的杏仁带来柔和而有个性的风味。', 'pt' => 'O sabor suave e marcante de amêndoas cuidadosamente selecionadas.'],
            'yer-fistigi-ezmesi' => ['tr' => 'Yoğun fıstık tadı ve parçacıklı dokusuyla günün her anına eşlik eder.', 'en' => 'A full peanut flavour and crunchy texture for every moment of the day.', 'de' => 'Voller Erdnussgeschmack und eine knackige Textur für jeden Moment des Tages.', 'fr' => 'Un goût intense de cacahuète et une texture croquante pour chaque moment de la journée.', 'es' => 'Sabor intenso a cacahuete y textura crujiente para cada momento del día.', 'it' => 'Un gusto pieno di arachidi e una consistenza croccante per ogni momento della giornata.', 'ru' => 'Насыщенный вкус арахиса и хрустящая текстура на каждый момент дня.', 'ar' => 'نكهة فول سوداني غنية وقوام مقرمش يرافقك في كل لحظة من اليوم.', 'zh' => '浓郁花生风味与颗粒口感，适合一天中的每个时刻。', 'pt' => 'Sabor intenso de amendoim e textura crocante para todos os momentos do dia.'],
            'seker-ilavesiz-yer-fistigi-ezmesi' => ['tr' => 'Şeker ilavesiz formülüyle sade, güçlü ve parçacıklı lezzet.', 'en' => 'A pure, powerful and crunchy recipe with no added sugar.', 'de' => 'Ein purer, kräftiger und knackiger Genuss ohne Zuckerzusatz.', 'fr' => 'Une recette pure, intense et croquante, sans sucre ajouté.', 'es' => 'Una receta pura, intensa y crujiente sin azúcar añadido.', 'it' => 'Una ricetta pura, intensa e croccante senza zuccheri aggiunti.', 'ru' => 'Чистый, насыщенный и хрустящий рецепт без добавления сахара.', 'ar' => 'وصفة بسيطة وغنية ومقرمشة من دون سكر مضاف.', 'zh' => '不添加糖，配方纯粹、浓郁且富有颗粒口感。', 'pt' => 'Uma receita pura, intensa e crocante, sem adição de açúcar.'],
            'hindistan-cevizi-ezmesi' => ['tr' => 'Hafif, aromatik ve tropikal bir lezzet.', 'en' => 'A light, aromatic taste with a tropical character.', 'de' => 'Ein leichter, aromatischer Genuss mit tropischem Charakter.', 'fr' => 'Une saveur légère, aromatique et tropicale.', 'es' => 'Un sabor ligero, aromático y tropical.', 'it' => 'Un gusto leggero, aromatico e tropicale.', 'ru' => 'Лёгкий, ароматный вкус с тропическим характером.', 'ar' => 'مذاق خفيف وعطري بطابع استوائي.', 'zh' => '轻盈芳香，带有热带风味。', 'pt' => 'Um sabor leve, aromático e tropical.'],
        ];

        return $descriptions[$key];
    }

    /** @return array<string, string> */
    private function ingredients(string $key, int $percentage): array
    {
        $primaryIngredients = [
            'findik-kremasi' => ['tr' => 'Fındık', 'en' => 'Hazelnuts', 'de' => 'Haselnüsse', 'fr' => 'Noisettes', 'es' => 'Avellanas', 'it' => 'Nocciole', 'ru' => 'Фундук', 'ar' => 'بندق', 'zh' => '榛子', 'pt' => 'Avelãs'],
            'antep-fistikli-kremasi' => ['tr' => 'Antep Fıstığı', 'en' => 'Pistachios', 'de' => 'Pistazien', 'fr' => 'Pistaches', 'es' => 'Pistachos', 'it' => 'Pistacchi', 'ru' => 'Фисташки', 'ar' => 'فستق', 'zh' => '开心果', 'pt' => 'Pistácios'],
            'badem-ezmesi' => ['tr' => 'Badem', 'en' => 'Almonds', 'de' => 'Mandeln', 'fr' => 'Amandes', 'es' => 'Almendras', 'it' => 'Mandorle', 'ru' => 'Миндаль', 'ar' => 'لوز', 'zh' => '杏仁', 'pt' => 'Amêndoas'],
            'yer-fistigi-ezmesi' => ['tr' => 'Yer Fıstığı', 'en' => 'Peanuts', 'de' => 'Erdnüsse', 'fr' => 'Cacahuètes', 'es' => 'Cacahuetes', 'it' => 'Arachidi', 'ru' => 'Арахис', 'ar' => 'فول سوداني', 'zh' => '花生', 'pt' => 'Amendoins'],
            'seker-ilavesiz-yer-fistigi-ezmesi' => ['tr' => 'Yer Fıstığı', 'en' => 'Peanuts', 'de' => 'Erdnüsse', 'fr' => 'Cacahuètes', 'es' => 'Cacahuetes', 'it' => 'Arachidi', 'ru' => 'Арахис', 'ar' => 'فول سوداني', 'zh' => '花生', 'pt' => 'Amendoins'],
            'hindistan-cevizi-ezmesi' => ['tr' => 'Hindistan Cevizi', 'en' => 'Coconut', 'de' => 'Kokosnuss', 'fr' => 'Noix de coco', 'es' => 'Coco', 'it' => 'Cocco', 'ru' => 'Кокос', 'ar' => 'جوز الهند', 'zh' => '椰子', 'pt' => 'Coco'],
        ];
        $base = [
            'tr' => "{$primaryIngredients[$key]['tr']} (%{$percentage}), Pancar Şekeri, Yağlı Süt Tozu, Peynir Altı Suyu Tozu, Bitkisel Yağ (Pamuk), Emülgatör (Ayçiçek Lesitini).",
            'en' => "{$primaryIngredients[$key]['en']} ({$percentage}%), beet sugar, whole milk powder, whey powder, vegetable oil (cottonseed), emulsifier (sunflower lecithin).",
            'de' => "{$primaryIngredients[$key]['de']} ({$percentage} %), Rübenzucker, Vollmilchpulver, Molkenpulver, Pflanzenöl (Baumwollsaat), Emulgator (Sonnenblumenlecithin).",
            'fr' => "{$primaryIngredients[$key]['fr']} ({$percentage} %), sucre de betterave, lait entier en poudre, lactosérum en poudre, huile végétale (coton), émulsifiant (lécithine de tournesol).",
            'es' => "{$primaryIngredients[$key]['es']} ({$percentage} %), azúcar de remolacha, leche entera en polvo, suero de leche en polvo, aceite vegetal (algodón), emulgente (lecitina de girasol).",
            'it' => "{$primaryIngredients[$key]['it']} ({$percentage}%), zucchero di barbabietola, latte intero in polvere, siero di latte in polvere, olio vegetale (cotone), emulsionante (lecitina di girasole).",
            'ru' => "{$primaryIngredients[$key]['ru']} ({$percentage} %), свекловичный сахар, сухое цельное молоко, сухая сыворотка, растительное масло (хлопковое), эмульгатор (подсолнечный лецитин).",
            'ar' => "{$primaryIngredients[$key]['ar']} ({$percentage}٪)، سكر الشمندر، حليب كامل الدسم مجفف، مسحوق مصل اللبن، زيت نباتي (بذور القطن)، مستحلب (ليسيثين دوار الشمس).",
            'zh' => "{$primaryIngredients[$key]['zh']}（{$percentage}%）、甜菜糖、全脂奶粉、乳清粉、植物油（棉籽油）、乳化剂（葵花籽卵磷脂）。",
            'pt' => "{$primaryIngredients[$key]['pt']} ({$percentage}%), açúcar de beterraba, leite gordo em pó, soro de leite em pó, óleo vegetal (algodão), emulsionante (lecitina de girassol).",
        ];

        if ($key !== 'seker-ilavesiz-yer-fistigi-ezmesi') {
            return $base;
        }

        return [
            'tr' => 'Yer Fıstığı (%72), Yağlı Süt Tozu, Peynir Altı Suyu Tozu, Bitkisel Yağ (Pamuk), Emülgatör (Ayçiçek Lesitini).',
            'en' => 'Peanuts (72%), whole milk powder, whey powder, vegetable oil (cottonseed), emulsifier (sunflower lecithin).',
            'de' => 'Erdnüsse (72 %), Vollmilchpulver, Molkenpulver, Pflanzenöl (Baumwollsaat), Emulgator (Sonnenblumenlecithin).',
            'fr' => 'Cacahuètes (72 %), lait entier en poudre, lactosérum en poudre, huile végétale (coton), émulsifiant (lécithine de tournesol).',
            'es' => 'Cacahuetes (72 %), leche entera en polvo, suero de leche en polvo, aceite vegetal (algodón), emulgente (lecitina de girasol).',
            'it' => 'Arachidi (72%), latte intero in polvere, siero di latte in polvere, olio vegetale (cotone), emulsionante (lecitina di girasole).',
            'ru' => 'Арахис (72 %), сухое цельное молоко, сухая сыворотка, растительное масло (хлопковое), эмульгатор (подсолнечный лецитин).',
            'ar' => 'فول سوداني (72٪)، حليب كامل الدسم مجفف، مسحوق مصل اللبن، زيت نباتي (بذور القطن)، مستحلب (ليسيثين دوار الشمس).',
            'zh' => '花生（72%）、全脂奶粉、乳清粉、植物油（棉籽油）、乳化剂（葵花籽卵磷脂）。',
            'pt' => 'Amendoins (72%), leite gordo em pó, soro de leite em pó, óleo vegetal (algodão), emulsionante (lecitina de girassol).',
        ];
    }

    /** @return array<string, string> */
    private function allergens(string $key): array
    {
        $allergens = $key === 'hindistan-cevizi-ezmesi'
            ? ['tr' => 'Süt ve süt ürünleri, Fındık, Badem, Yer Fıstığı, Antep Fıstığı.', 'en' => 'Milk and milk products, hazelnuts, almonds, peanuts and pistachios.', 'de' => 'Milch und Milcherzeugnisse, Haselnüsse, Mandeln, Erdnüsse und Pistazien.', 'fr' => 'Lait et produits laitiers, noisettes, amandes, cacahuètes et pistaches.', 'es' => 'Leche y productos lácteos, avellanas, almendras, cacahuetes y pistachos.', 'it' => 'Latte e derivati, nocciole, mandorle, arachidi e pistacchi.', 'ru' => 'Молоко и молочные продукты, фундук, миндаль, арахис и фисташки.', 'ar' => 'الحليب ومنتجاته، البندق، اللوز، الفول السوداني والفستق.', 'zh' => '含牛奶及乳制品、榛子、杏仁、花生和开心果。', 'pt' => 'Leite e produtos lácteos, avelãs, amêndoas, amendoins e pistácios.']
            : ['tr' => 'Süt ve süt ürünleri; fındık, badem, yer fıstığı ve Antep fıstığı içerebilir.', 'en' => 'Contains milk and milk products; may contain hazelnuts, almonds, peanuts and pistachios.', 'de' => 'Enthält Milch und Milcherzeugnisse; kann Haselnüsse, Mandeln, Erdnüsse und Pistazien enthalten.', 'fr' => 'Contient du lait et des produits laitiers ; peut contenir des noisettes, des amandes, des cacahuètes et des pistaches.', 'es' => 'Contiene leche y productos lácteos; puede contener avellanas, almendras, cacahuetes y pistachos.', 'it' => 'Contiene latte e derivati; può contenere nocciole, mandorle, arachidi e pistacchi.', 'ru' => 'Содержит молоко и молочные продукты; может содержать фундук, миндаль, арахис и фисташки.', 'ar' => 'يحتوي على الحليب ومنتجاته؛ وقد يحتوي على البندق واللوز والفول السوداني والفستق.', 'zh' => '含牛奶及乳制品；可能含有榛子、杏仁、花生和开心果。', 'pt' => 'Contém leite e produtos lácteos; pode conter avelãs, amêndoas, amendoins e pistácios.'];

        return $allergens;
    }

    /** @return array<string, string> */
    private function standardPackagingDetails(): array
    {
        return [
            'jar.net_weight' => '250 gr',
            'jar.gross_weight' => '400 gr',
            'jar.diameter' => '74 mm',
            'jar.height' => '85 mm',
            'carton.units' => '12',
            'carton.net_weight' => '3 kg',
            'carton.gross_weight' => '5,65 kg',
            'carton.dimensions' => '255 × 325 × 115 mm',
            'pallet.cartons' => '100',
            'pallet.net_weight' => '300 kg',
            'pallet.gross_weight' => '580 kg',
            'industrial.type' => 'EUP (Europalette)',
            'industrial.dimensions' => '80 cm × 120 cm',
            'industrial.dimensions_inches' => '31,5″ × 47,24″',
            'industrial.buckets_on_pallet' => '24',
            'industrial.net_weight' => '480 kg',
            'industrial.gross_weight' => '520 kg',
            'industrial.loaded_height' => '100 cm',
            'industrial.loaded_height_inches' => '39,36″',
            'dimensions.d1' => '326,0 mm',
            'dimensions.d2' => '311,0 mm',
            'dimensions.d3' => '286,0 mm',
            'dimensions.h' => '274,0 mm',
        ];
    }
}
