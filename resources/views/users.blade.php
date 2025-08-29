@extends('template')

@section('content')
    <div class="container-flud">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>


        <style>
            #loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.9);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .dots {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
            }

            .dots span {
                width: 14px;
                height: 14px;
                background: linear-gradient(135deg, #4aa3ff, #00e0ff);
                border-radius: 50%;
                display: inline-block;
                animation: bounce 0.8s infinite ease-in-out;
            }

            .dots span:nth-child(1) {
                animation-delay: 0s;
            }

            .dots span:nth-child(2) {
                animation-delay: 0.2s;
            }

            .dots span:nth-child(3) {
                animation-delay: 0.4s;
            }
            h3{
                text-align: center;
                font-weight:bold;
                color:#645757;
                font-size: ;
            }

            @keyframes bounce {

                0%,
                80%,
                100% {
                    transform: translateY(0);
                }

                40% {
                    transform: translateY(-12px);
                }
            }

            .loader-text {
                margin-top: 15px;
                font-size: 1.2rem;
                font-weight: bold;
                color: #333;
                letter-spacing: 1px;
                animation: fade 1.5s infinite alternate;
            }

            @keyframes fade {
                from {
                    opacity: 0.5;
                }

                to {
                    opacity: 1;
                }
            }
        </style>

        <h3>User List</h3>

        <div id="loader">
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p class="loader-text">⚡ Fetching users... Hold tight ⚡</p>
        </div>

        <table id="userTable" class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th width="5%">ID</th>
                    <th width="25%">Name</th>
                    <th width="30%">Email</th>
                    <th width="20%">Action</th>
                </tr>
            </thead>
            <tbody id="userData">

            </tbody>
        </table>
    </div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">


            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">


                    <input type="hidden" id="edit_user_id">
                    <div class="form-group">
                        <label for="edit_name">Name:</label>
                        <input type="text" class="form-control" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email:</label>
                        <input type="email" class="form-control" id="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_pwd">Password:</label>
                        <input type="password" class="form-control" id="edit_pwd">
                    </div>
                    <button id="updateUserBtn" type="submit" class="btn btn-primary">Update</button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.css"></script>
    <script>
        $(document).ready(function () {
            loadUsers();

            function loadUsers() {
                $("#loader").show();
                $("#userData").hide();

                $.ajax({
                    url: "{{ url('get_users') }}",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        $("#loader").hide();
                        $("#userData").show();

                        let html = "";
                        if (response.status && response.users.length > 0) {
                            response.users.forEach(user => {
                                html += `<tr id="row-${user.id}">
                                            <td>${user.id}</td>
                                            <td class="user-name">${user.name}</td>
                                            <td class="user-email">${user.email}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info">View</button>
                                                <button class="btn btn-sm btn-warning editUser" 
                                                    data-id="${user.id}" 
                                                    data-name="${user.name}" 
                                                    data-email="${user.email}" 
                                                    type="button" 
                                                    data-toggle="modal" 
                                                    data-target="#myModal">Edit</button>
                                                <button class="btn btn-sm btn-danger deleteUser" 
                                                    data-name="${user.name}" 
                                                    data-id="${user.id}">Delete</button>
                                            </td>
                                        </tr>`;
                            });
                        } else {
                            html = `<tr>
                                        <td colspan="4" class="text-center text-danger">No data found</td>
                                    </tr>`;
                        }
                        $("#userData").html(html);

                        // Reset DataTable properly
                        if ($.fn.DataTable.isDataTable('#userTable')) {
                            $('#userTable').DataTable().destroy();
                        }
                        $('#userTable').DataTable({
                            order: [[0, 'asc']]
                        });
                    },
                    error: function (xhr) {
                        $("#loader").hide();
                        $("#userData").html("<tr><td colspan='4' class='text-danger text-center'>Something went wrong!</td></tr>");
                        console.log(xhr.responseText);
                    }
                });
            }

            // DELETE USER
            $(document).on('click', '.deleteUser', function () {
                let button = $(this);
                let userId = $(this).data("id");
                let userName = $(this).data("name");
                if (confirm("Are you sure you want to delete " + userName + "?")) {
                    $.ajax({
                        url: "{{ url('delete_user') }}/" + userId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.status) {
                                alert(response.message);
                                button.closest("tr").fadeOut(500, function () {
                                    $(this).remove();
                                });
                            } else {
                                alert("Failed to delete user!");
                            }
                        },
                        error: function (xhr) {
                            console.log(xhr.responseText);
                            alert("Error deleting user!");
                        }
                    });
                }
            });

  
            $(document).on('click', '.editUser', function () {
                let userId = $(this).data('id');
                let userName = $(this).data('name');
                let userEmail = $(this).data('email');

                $('#edit_user_id').val(userId);
                $('#edit_name').val(userName);
                $('#edit_email').val(userEmail);
                $('#edit_pwd').val('');
            });

    
            $('#updateUserBtn').on('click', function () {
                let userId = $('#edit_user_id').val();
                let name = $('#edit_name').val();
                let email = $('#edit_email').val();
                let password = $('#edit_pwd').val();

                $.ajax({
                    url: '/update_user/' + userId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: name,
                        email: email,
                        password: password
                    },
                    success: function (response) {
                        if (response.status) {
                            // 🔥 Update only the row that was edited
                            let row = $('#row-' + userId);
                            row.find('.user-name').text(response.users.name);
                            row.find('.user-email').text(response.users.email);

                            $('#myModal').removeClass('show').hide();
                            $('.modal-backdrop').remove(); 
                            $('body').removeClass('modal-open');

                    
                        } else {
                            alert('Update failed!');
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('Something went wrong!');
                    }
                });
            });

        });

    </script>

@endsection