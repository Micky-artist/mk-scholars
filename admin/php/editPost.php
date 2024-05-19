<?php
if((isset($_GET['i']) && !empty(isset($_GET['i'])) && (isset($_GET['n']) && !empty(isset($_GET['n']))) )){
    $PostId=$_GET['i'];
    // $serviceId=$_GET['n'];
    $selectPostDesc = mysqli_query($conn, "SELECT * FROM posts WHERE postId=$PostId");
    if ($selectPostDesc->num_rows > 0) {
        $getPostDesc = mysqli_fetch_assoc($selectPostDesc);
        if(!isset($_GET['n']) || empty(isset($_GET['n'])) || $_GET['n']!=$getPostDesc['postTitle']){
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

