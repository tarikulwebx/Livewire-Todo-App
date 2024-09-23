<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:30')]
    public $name;

    public $search;

    public $editingTodoId;

    #[Validate('required|min:3|max:30')]
    public $editingTodoName;

    public function create()
    {
        // validate
        // create the todo
        // clear the input
        // send flash message

        $validated = $this->validateOnly('name');
        Todo::create($validated);

        $this->reset('name');

        session()->flash('success', 'Created.');

        $this->resetPage();
    }

    public function delete($todoId)
    {
        try {
            $todo = Todo::findOrFail($todoId);
            $todo->delete();
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete todo.');
            return;
        }
    }

    public function toggle(Todo $todo)
    {
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit(Todo $todo)
    {
        $this->editingTodoId = $todo->id;
        $this->editingTodoName = $todo->name;
    }

    public function cancelEdit()
    {
        $this->reset('editingTodoId', 'editingTodoName');
    }

    public function update(Todo $todo)
    {
        $this->validateOnly('editingTodoName');
        $todo->update(['name' => $this->editingTodoName]);

        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
