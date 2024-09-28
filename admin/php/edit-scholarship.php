<?php
if((isset($_GET['i']) && !empty(isset($_GET['i'])) && (isset($_GET['n']) && !empty(isset($_GET['n']))) )){
    $serviceId=$_GET['i'];
    // $serviceId=$_GET['n'];
    $selectServicesDesc = mysqli_query($conn, "SELECT * FROM services WHERE serviceId=$serviceId");
    if ($selectServicesDesc->num_rows > 0) {
        $getServiceDesc = mysqli_fetch_assoc($selectServicesDesc);
        if(!isset($_GET['n']) || empty(isset($_GET['n'])) || $_GET['n']!=$getServiceDesc['servicename']){
            echo('
            <script type="text/javascript">
            window.location.href="404";
            </script>
            ');
        }
    }else{
        echo('
    <script type="text/javascript">
    window.location.href="404";
    </script>
    ');
    }
}else{
    echo('
<script type="text/javascript">
window.location.href="404";
</script>
');
}

