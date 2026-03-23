<?php

use Illuminate\Support\Facades\Schema;

describe('hashIdMorphs', function () {
    it('creates type and id columns with an index', function () {
        Schema::dropIfExists('taggables');
        Schema::create('taggables', function ($table) {
            $table->id();
            $table->hashIdMorphs('taggable');
        });

        $columns = Schema::getColumnListing('taggables');
        expect($columns)->toContain('taggable_type');
        expect($columns)->toContain('taggable_id');
    });

    it('supports custom index name', function () {
        Schema::dropIfExists('commentables');
        Schema::create('commentables', function ($table) {
            $table->id();
            $table->hashIdMorphs('commentable', 'custom_morph_index');
        });

        $columns = Schema::getColumnListing('commentables');
        expect($columns)->toContain('commentable_type');
        expect($columns)->toContain('commentable_id');
    });
});

describe('nullableHashIdMorphs', function () {
    it('creates nullable type and id columns with an index', function () {
        Schema::dropIfExists('imageables');
        Schema::create('imageables', function ($table) {
            $table->id();
            $table->nullableHashIdMorphs('imageable');
        });

        $columns = Schema::getColumnListing('imageables');
        expect($columns)->toContain('imageable_type');
        expect($columns)->toContain('imageable_id');
    });

    it('allows null values in morph columns', function () {
        Schema::dropIfExists('imageables');
        Schema::create('imageables', function ($table) {
            $table->id();
            $table->nullableHashIdMorphs('imageable');
        });

        // Insert a row with null morph values — should not throw
        \Illuminate\Support\Facades\DB::table('imageables')->insert([
            'imageable_type' => null,
            'imageable_id' => null,
        ]);

        $row = \Illuminate\Support\Facades\DB::table('imageables')->first();
        expect($row->imageable_type)->toBeNull();
        expect($row->imageable_id)->toBeNull();
    });
});
