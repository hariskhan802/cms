<div class="modal fade" id="add-edit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
    <form method="post" action="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-wrap">
                    
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" placeholder="Name" name="name" required>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" placeholder="Email" name="email" required>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group password-field">
                            <label>Password</label>
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group img-f-g">
                            <label>Image</label>
                            <input type="file" name="image" accept="image/*"  required />
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->role }}</option>
                                @endforeach
                            </select>
                            <small class="error-msg"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <input type="hidden" name="_status">
                    <input type="hidden" name="_image">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" name="submit" value="Add">
                </div>
                <div class="card mb-4 c-msg border-left-success">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>