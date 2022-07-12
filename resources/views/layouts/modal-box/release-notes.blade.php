<style>
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
    }

    /* The Close Button */
    .close {
        color: #000!important;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #myModal th{
        color: #dd2c00;
    }
    #myModal td{
        font-weight: bold;
    }
</style>

<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" style="font-size: 16px!important;">X</span>
        <h5 style="text-align: center;margin-bottom: 20px;">Release Notes For CityZore Beta v1.1</h5>
        <table>
            <thead>
            <th>
                Name
            </th>
            <th>
                Type
            </th>
            <th>
                Description
            </th>
            <th>
                Date
            </th>
            </thead>
            <tbody>
            <tr>
                <td>Commissioner System</td>
                <td>Improvements</td>
                <td>Commissioners can booked our products with their changeable commission rates</td>
                <td>11.02.2020</td>
            </tr>
            <tr>
                <td>Sharable Shopping Cart System</td>
                <td>New Feature</td>
                <td>Commissioners can share their cart with their customers for checkout via whatsapp or email.</td>
                <td>11.02.2020</td>
            </tr>
            </tbody>
        </table>
    </div>

</div>

<script>
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("releaseNotes");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
