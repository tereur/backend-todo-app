<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test the task index endpoint.
     */
    public function test_user_can_retrieve_all_tasks()
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([['id', 'name', 'description', 'isMine']]);
    }

    /**
     * Test storing a task with an image.
     */
    public function test_user_can_create_task_with_image()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('task.jpg');

        $data = [
            'name' => 'Test Task',
            'description' => 'Task description',
            'latitude' => 12.34,
            'longitude' => 56.78,
            'image' => $image,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Task']);

        Storage::disk('public')->assertExists('images/' . $image->hashName());
    }

    /**
     * Test updating a task.
     */
    public function test_user_can_update_their_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'name' => 'Updated Task',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Task']);
    }

    /**
     * Test unauthorized update attempt.
     */
    public function test_user_cannot_update_someone_else_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $data = ['name' => 'Unauthorized Update'];

        $response = $this->actingAs($this->user)
            ->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthorized']);
    }

    /**
     * Test task deletion.
     */
    public function test_user_can_delete_their_task()
    {
        Storage::fake('public');

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'image' => 'images/sample.jpg',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted']);
    }

    /**
     * Test unauthorized task deletion.
     */
    public function test_user_cannot_delete_someone_else_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthorized']);
    }
}
