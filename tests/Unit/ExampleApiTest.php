<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExampleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_documents()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Document::factory()->count(5)->create();

        $this->actingAs($admin)
            ->getJson('/api/documents')
            ->assertStatus(200)
            ->assertJsonCount(5);
    }

    public function test_store_document_successfully()
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->postJson('/api/documents', [
                'title' => 'Test Document',
                'file' => $file,
                'description' => 'A test document description',
                'category_id' => 1,
            ])
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'file_path']);

        Storage::disk('local')->assertExists('documents/Test Document.pdf');
    }
}
