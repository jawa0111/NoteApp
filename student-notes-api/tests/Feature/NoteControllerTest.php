<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a note with valid data.
     */
    public function test_create_note_with_valid_data(): void
    {
        $payload = [
            'title' => 'Test Note',
            'content' => 'This is test content',
            'priority' => 'high',
            'is_archived' => false,
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Note created',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'content',
                    'priority',
                    'is_archived',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('notes', [
            'title' => 'Test Note',
            'content' => 'This is test content',
            'priority' => 'high',
            'is_archived' => false,
        ]);
    }

    /**
     * Test creating a note with missing title fails validation.
     */
    public function test_create_note_with_missing_title_fails_validation(): void
    {
        $payload = [
            'content' => 'This is test content',
            'priority' => 'medium',
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    /**
     * Test creating a note with missing content fails validation.
     */
    public function test_create_note_with_missing_content_fails_validation(): void
    {
        $payload = [
            'title' => 'Test Note',
            'priority' => 'low',
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('content');
    }

    /**
     * Test creating a note with invalid priority fails validation.
     */
    public function test_create_note_with_invalid_priority_fails_validation(): void
    {
        $payload = [
            'title' => 'Test Note',
            'content' => 'Test content',
            'priority' => 'critical', // invalid
        ];

        $response = $this->postJson('/api/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('priority');
    }

    /**
     * Test archiving a note sets is_archived to true.
     */
    public function test_archive_note(): void
    {
        $note = Note::factory()->create([
            'title' => 'Note to Archive',
            'is_archived' => false,
        ]);

        $response = $this->patchJson("/api/notes/{$note->id}/archive");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Note archived',
            ]);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'is_archived' => true,
        ]);
    }

    /**
     * Test archiving a non-existent note returns 404.
     */
    public function test_archive_non_existent_note_returns_404(): void
    {
        $response = $this->patchJson('/api/notes/9999/archive');

        $response->assertStatus(404);
    }

    /**
     * Test getting all notes.
     */
    public function test_get_all_notes(): void
    {
        Note::factory()->count(3)->create();

        $response = $this->getJson('/api/notes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'priority',
                        'is_archived',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test filtering notes by priority.
     */
    public function test_filter_notes_by_priority(): void
    {
        Note::factory()->count(2)->create(['priority' => 'high']);
        Note::factory()->count(1)->create(['priority' => 'low']);

        $response = $this->getJson('/api/notes?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $note) {
            $this->assertEquals('high', $note['priority']);
        }
    }
}
