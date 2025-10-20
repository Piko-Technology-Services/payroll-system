<div class="modal fade" id="rulesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Earnings & Deductions Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Add Earning Rule --}}
                    <div class="col-md-6">
                        <h6>Add Earning Rule</h6>
                        <form action="{{ route('earningRules.store') }}" method="POST" class="d-flex gap-2 align-items-end mb-3">
                            @csrf
                            <input type="text" name="name" class="form-control" placeholder="Earning Name" required>
                            <input type="number" step="0.01" name="default_value" class="form-control" placeholder="Default Value" required>
                            <select name="type" class="form-select">
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                            <button class="btn btn-success" type="submit">Add</button>
                        </form>
                        {{-- List & Update Earnings --}}
                        @foreach($earningRules as $rule)
                        <form action="{{ route('earningRules.update', $rule->id) }}" method="POST" class="d-flex gap-2 align-items-end mb-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $rule->name }}" class="form-control" required>
                            <input type="number" step="0.01" name="default_value" value="{{ $rule->default_value }}" class="form-control" required>
                            <select name="type" class="form-select">
                                <option value="fixed" {{ $rule->type=='fixed' ? 'selected':'' }}>Fixed</option>
                                <option value="percentage" {{ $rule->type=='percentage' ? 'selected':'' }}>Percentage</option>
                            </select>
                            <button class="btn btn-primary">Save</button>
                            <a href="{{ route('earningRules.destroy', $rule->id) }}" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a>
                        </form>
                        @endforeach
                    </div>

                    {{-- Add Deduction Rule --}}
                    <div class="col-md-6">
                        <h6>Add Deduction Rule</h6>
                        <form action="{{ route('deductionRules.store') }}" method="POST" class="d-flex gap-2 align-items-end mb-3">
                            @csrf
                            <input type="text" name="name" class="form-control" placeholder="Deduction Name" required>
                            <input type="number" step="0.01" name="default_value" class="form-control" placeholder="Default Value" required>
                            <select name="type" class="form-select">
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                            <button class="btn btn-success" type="submit">Add</button>
                        </form>
                        {{-- List & Update Deductions --}}
                        @foreach($deductionRules as $rule)
                        <form action="{{ route('deductionRules.update', $rule->id) }}" method="POST" class="d-flex gap-2 align-items-end mb-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $rule->name }}" class="form-control" required>
                            <input type="number" step="0.01" name="default_value" value="{{ $rule->default_value }}" class="form-control" required>
                            <select name="type" class="form-select">
                                <option value="fixed" {{ $rule->type=='fixed' ? 'selected':'' }}>Fixed</option>
                                <option value="percentage" {{ $rule->type=='percentage' ? 'selected':'' }}>Percentage</option>
                            </select>
                            <button class="btn btn-primary">Save</button>
                            <a href="{{ route('deductionRules.destroy', $rule->id) }}" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a>
                        </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
