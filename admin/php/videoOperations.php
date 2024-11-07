<?php
#INSERT TAGS
if (isset($_POST['addVid'])) {
    $vidTitle = mysqli_real_escape_string($conn, $_POST['vidTitle']);
    $vidLink = mysqli_real_escape_string($conn, $_POST['vidLink']);
    $vidStatus = mysqli_real_escape_string($conn, $_POST['vidStatus']);

    $InsertVideo = mysqli_query($conn, "INSERT INTO youtubeVideos(videoLink,VideoTitle,VideoStatus) VALUES ('$vidLink','$vidTitle',$vidStatus)");
    if ($InsertVideo) {
        echo "Tag Insterted";
    } else {
        echo "Tag not inserted";
    }
}


#DELETE TAGS

// if (isset($_GET['deleteTag'])) {
//     $tagId = $_GET['deleteTag'];

//     $deleteTag = mysqli_query($conn, "DELETE FROM PostTags WHERE Tagid=$tagId");
//     if ($deleteTag) {
//         echo "Tag Deleted";
//     } else {
//         echo "Tag not Deleted";
//     }
// }

#EDIT YOUTUBE VIDEO
if (isset($_GET['Edit'])) {
    $videoId = $_GET['Edit'];
    $editVideo = mysqli_query($conn, "SELECT * FROM youtubeVideos WHERE videoId=$videoId");
    if ($editVideo->num_rows > 0) {
        while ($editVideoData = mysqli_fetch_assoc($editVideo)) {
            $vidTitle = $editVideoData['VideoTitle'];
            $videoLink = $editVideoData['videoLink'];
            $vidId = $editVideoData['videoId'];
        }
    }
}

#UPDATE YOUTUVE VIDEO
if (isset($_POST['UpdateVid'])) {
    $vidId = mysqli_real_escape_string($conn, $_POST['vidId']);
    $vidTitle = mysqli_real_escape_string($conn, $_POST['vidTitle']);
    $vidLink = mysqli_real_escape_string($conn, $_POST['vidLink']);
    $vidStatus = mysqli_real_escape_string($conn, $_POST['vidStatus']);

    $updateVideo = mysqli_query($conn, "UPDATE youtubeVideos SET videoLink='$vidLink',VideoTitle='$vidTitle' ,VideoStatus=$vidStatus WHERE videoId=$vidId");
}

if (isset($_GET['Activate'])) {
    $vidId = $_GET['Activate'];
    $deleteTag = mysqli_query($conn, "UPDATE youtubeVideos SET VideoStatus=1 WHERE videoId=$vidId");
}
if (isset($_GET['DeActivate'])) {
    $vidId = $_GET['DeActivate'];
    $deleteTag = mysqli_query($conn, "UPDATE youtubeVideos SET VideoStatus=0 WHERE videoId=$vidId");
}
if (isset($_GET['Delete'])) {
    $vidId = $_GET['Delete'];
    $deleteTag = mysqli_query($conn, "DELETE FROM youtubeVideos WHERE videoId=$vidId");
}
