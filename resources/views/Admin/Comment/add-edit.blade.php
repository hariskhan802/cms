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
                            <label>Comment</label>
                            <textarea name="comment" class="form-control" placeholder="Comment"></textarea>
                            <small class="error-msg"></small>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status"  class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="approved">Approved</option>
                                <option value="pending">Pending</option>
                                <option value="spam">Spam</option>
                                
                            </select>
                            <small class="error-msg"></small>
                        </div>
                        
                        
                    </div>
                </div>
                <div class="modal-footer">
                    {{ csrf_field() }}
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" name="submit" value="Update">
                </div>
                <div class="card mb-4 c-msg border-left-success">
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>