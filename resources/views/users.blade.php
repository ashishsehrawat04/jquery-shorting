@extends('template')

@section('content')
<div class="container">

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

  .dots span:nth-child(1) { animation-delay: 0s; }
  .dots span:nth-child(2) { animation-delay: 0.2s; }
  .dots span:nth-child(3) { animation-delay: 0.4s; }

  @keyframes bounce {
    0%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-12px); }
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
    from { opacity: 0.5; }
    to { opacity: 1; }
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

{{-- jQuery + DataTables --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src ="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.css"></script>
<script>
$(document).ready(function(){
    loadUsers();

    function loadUsers(){
        $("#loader").show(); 
        $("#userData").hide(); 

        $.ajax({
            url: "{{ url('get_users') }}", 
            type: "GET",
            dataType: "json",
            success: function (response) {
                $("#loader").hide();  
                $("#userData").show();

                let html = "";
                if (response.status && response.users.length > 0) {
                    response.users.forEach(user => {
                        html += `<tr>
                                    <td>${user.id}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info">View</button>
                                        <button class="btn btn-sm btn-warning">Edit</button>
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </td>
                                </tr>`;
                    });
                } else {
                    html = `<tr>
                                <td colspan="4" class="text-center text-danger">No data found</td>
                            </tr>`;
                }
                $("#userData").html(html);

      
                $('#userTable').DataTable({
                    destroy: true, 
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
});
</script>
@endsection
