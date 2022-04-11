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
                            <label>Title</label>
                            <input type="text" class="form-control" placeholder="Role" name="role" required>
                            <small class="error-msg"></small>
                        </div>
                        @foreach(__get_roles_permissions() as $key2 => $role_p)
                                    
                        <div class="form-group-wrap">
                            <div class="form-c-wrap">
                                <label class="m-label">{{ ucfirst($key2) }}</label>
                                <input type="checkbox" class="checked-roles">
                                @foreach($role_p as $key3 => $r)
                                <?php // var_dump($r) ?>
                                
                                <div class="form-group checkbox-fg">
                                    <input type="checkbox" class="form-control"  name="{{ 'permissions['.$r.']' }}" id="{{ 'permissions['.$r.']' }}" >
                                    <label for="{{ 'permissions['.$r.']' }}" class="c-label">{{ ucwords(str_replace('_', ' ', $r)) }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach                        
                    </div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <input type="hidden" name="_status">
                    <input type="hidden" name="_featured_image">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" name="submit" value="Publish">
                </div>
                <div class="card mb-4 c-msg border-left-success">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>