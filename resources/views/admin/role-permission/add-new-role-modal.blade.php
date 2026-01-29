{{-- ================= ADD / EDIT ROLE MODAL ================= --}}
<form class="row g-6" method="POST" action="{{ route('save_role') }}">
    @csrf
    <div class="modal fade" id="rolePermissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Role</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-0">

                    {{-- Role Name --}}
                    <div class="mb-3 px-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="role_name" class="form-control" required>
                    </div>

                    {{-- Permissions --}}

                    <div class="d-flex justify-content-between align-items-center flex-wrap pb-2 border-bottom px-3">
                        <h5 class="fw-semibold mb-0">Administrator Access</h5>
                        <div class="form-check me-4">
                            <input class="form-check-input" type="checkbox" name="permissions[]" id="selectAllPermissions">
                            <label class="form-check-label">Select All</label>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">User Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View User">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create User">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="deleteCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete User">
                                <label for="deleteCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Roles Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Roles">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Roles">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Roles">
                                <label for="createCountry" class="form-check-label">Edit</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Permission Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">
                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Permission">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Permission">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Partner Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Partner">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Partner">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete Partner">
                                <label for="createCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Country Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Country">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Country">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Country">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="deleteCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete Country">
                                <label for="deleteCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Pending Student</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Pending Student">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">University Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View University">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit University">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create University">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="deleteCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete University">
                                <label for="deleteCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Course Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Course">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Course">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Course">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="deleteCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete Course">
                                <label for="deleteCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Student Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Student">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Student">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Student">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="deleteCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete Student">
                                <label for="deleteCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Partner Student Management</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Partner Student">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Partner Student">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="deleteCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Delete Partner Student">
                                <label for="deleteCountry" class="form-check-label">Delete</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Application</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Application">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Application">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>
                            <div class="form-check me-4">
                                <input id="createCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Create Application">
                                <label for="createCountry" class="form-check-label">Create</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">Partner Application</h5>
                        <div class="d-flex justify-content-start flex-wrap">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View Partner Application">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit Partner Application">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>
                        </div>
                    </div>
                    <div class="pb-2 pt-2 border-bottom px-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">All Application</h5>
                        <div class="d-flex justify-content-start flex-wrap ">

                            <div class="form-check me-4">
                                <input id="viewCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="View All Application">
                                <label for="viewCountry" class="form-check-label">View</label>
                            </div>

                            <div class="form-check me-4">
                                <input id="editCountry" class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                    value="Edit All Application">
                                <label for="editCountry" class="form-check-label">Edit</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Role</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('selectAllPermissions');
    const permissions = document.querySelectorAll('.permission-checkbox');

    // Select / unselect all permissions
    selectAll.addEventListener('change', function () {
        permissions.forEach(cb => cb.checked = this.checked);
    });

    // Auto update Select All checkbox
    permissions.forEach(cb => {
        cb.addEventListener('change', function () {
            selectAll.checked = [...permissions].every(c => c.checked);
        });
    });

});
</script>
