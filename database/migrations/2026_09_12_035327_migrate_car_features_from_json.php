<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('cars')
            ->select(['id', 'features'])
            ->orderBy('id')
            ->chunkById(100, function ($cars): void {
                foreach ($cars as $car) {
                    $features = is_string($car->features)
                        ? json_decode($car->features, true)
                        : $car->features;

                    if (! is_array($features)) {
                        continue;
                    }

                    foreach ($features as $featureName) {
                        if (! is_string($featureName) || blank($featureName)) {
                            continue;
                        }

                        $name = Str::squish($featureName);
                        $normalizedName = Str::lower($name);
                        $featureId = DB::table('features')->where('normalized_name', $normalizedName)->value('id');

                        if ($featureId === null) {
                            $featureId = DB::table('features')->insertGetId([
                                'name' => $name,
                                'normalized_name' => $normalizedName,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        DB::table('car_feature')->insertOrIgnore([
                            'car_id' => $car->id,
                            'feature_id' => $featureId,
                        ]);
                    }
                }
            });

        Schema::table('cars', function (Blueprint $table): void {
            $table->dropColumn('features');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table): void {
            $table->json('features')->nullable();
        });

        DB::table('cars')->select('id')->orderBy('id')->chunkById(100, function ($cars): void {
            foreach ($cars as $car) {
                $features = DB::table('car_feature')
                    ->join('features', 'features.id', '=', 'car_feature.feature_id')
                    ->where('car_feature.car_id', $car->id)
                    ->orderBy('features.name')
                    ->pluck('features.name')
                    ->all();

                DB::table('cars')->where('id', $car->id)->update([
                    'features' => $features === [] ? null : json_encode($features),
                ]);
            }
        });
    }
};
