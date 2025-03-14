<?php

namespace App\Livewire;

use App\Models\Groupprice;
use App\Models\Level;
use Livewire\Component;

class EditGroupPrice extends Component
{
    public $group_id, $groups, $status, $level;

    protected $rules = [
        'groups' => 'required|string|max:255',
        'status' => 'required',
        'levelid' => 'required|numeric'
    ];

    // Load the data when the component is loaded
    public function mount($group_id)
    {
        $group = Groupprice::findOrFail($group_id);
        $this->group_id = $group->id;
        $this->groups = $group->groups;
        $this->status = $group->status;
        $this->level = $group->levelid;
    }

    // Save data without refreshing
    public function updateGroup()
    {
        $this->validate();

        $group = GroupPrice::find($this->group_id);
        $group->update([
            'groups' => $this->groups,
            'status' => $this->status,
            'levelid' => $this->level
        ]);

        session()->flash('message', 'Group price updated successfully!');
    }

    public function render()
    {
        $levels = Level::where('deleted', '0')->get();
        return view('livewire.edit-group-price',compact('levels'));
    }
}
