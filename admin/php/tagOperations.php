<?php
#INSERT TAGS
if (isset($_POST['addTag'])) {
    $tagName = mysqli_real_escape_string($conn, $_POST['tagName']);
    $tagValue = mysqli_real_escape_string($conn, $_POST['tagValue']);
    $tagStatus = mysqli_real_escape_string($conn, $_POST['tagStatus']);

    $InsertTags = mysqli_query($conn, "INSERT INTO PostTags(TagName,TagValue,TagStatus) VALUES ('$tagName','$tagValue',$tagStatus)");
    if ($InsertTags) {
        echo "Tag Insterted";
    } else {
        echo "Tag not inserted";
    }
}


#DELETE TAGS
if (isset($_GET['deleteTag'])) {
    $tagId=$_GET['deleteTag'];

    $deleteTag = mysqli_query($conn, "DELETE FROM PostTags WHERE Tagid=$tagId");
    if ($deleteTag) {
        echo "Tag Deleted";
    } else {
        echo "Tag not Deleted";
    }
}

if (isset($_GET['updateTag'])) {
    $tagId=$_GET['updateTag'];
    $tagName = mysqli_real_escape_string($conn, $_POST['tagName']);
    $tagValue = mysqli_real_escape_string($conn, $_POST['tagValue']);
    $tagStatus = mysqli_real_escape_string($conn, $_POST['tagStatus']);

    $deleteTag = mysqli_query($conn, "UPDATE PostTags SET TagName='$tagName',TagValue='$tagValue' ,TagStatus=$tagStatus WHERE Tagid=$tagId");
    if ($deleteTag) {
        echo "Tag Updated";
    } else {
        echo "Tag not Updated";
    }
}
?>


