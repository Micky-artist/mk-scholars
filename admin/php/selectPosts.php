<?php
$selectposts = mysqli_query($conn, "SELECT * FROM posts ORDER BY uploadDate DESC");
if ($selectposts->num_rows > 0) {
    while ($postData = mysqli_fetch_assoc($selectposts)) {
        ?>
        <div class="d-flex flex-row comment-row mt-0">
            <div class="p-2">
                <img width="100" src="./uploads/posts/<?php echo $postData['projectImg1'] ?>" />
            </div>
            <div class="comment-text w-100">
                <h6 class="font-medium">
                    <?php echo $postData['postTitle'] ?>
                </h6>
                <!-- <span class="mb-3 d-block">Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </span> -->
                <div class="comment-footer">
                    <span class="text-muted float-end">
                    <?php echo $postData['uploadDate'].' '.$postData['uploadTime'] ?>
                    </span>
                    <a
                        href="edit-post?edit=true&i=<?php echo $postData['postId'] ?>&n=<?php echo $postData['postTitle'] ?>">
                        <button type="button" class="btn btn-cyan btn-sm text-white">
                            Edit
                        </button>
                    </a>
                    <a
                       target="_blank" href="https://www.icyezainteriors.com/project-prev?i=<?php echo $postData['postId']?>&n=<?php echo $postData['postTitle']?>">
                        <button type="button" class="btn btn-warning btn-sm text-white">
                            View
                        </button>
                    </a>
                    <?php
                    if ($postData['projStatus'] == 0) {
                        ?>
                        <a href="./php/actions?a=publishPost&i=<?php echo $postData['postId']?>&n=<?php echo $postData['postTitle']?>">
                        <button name="publishPost" class="btn btn-success btn-sm text-white">
                            Publish
                        </button>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="./php/actions?a=unPublishPost&i=<?php echo $postData['postId']?>&n=<?php echo $postData['postTitle']?>">
                        <button name="unPublishPost" class="btn btn-success btn-sm text-white">
                            Un Publish
                        </button>
                        </a>
                        <?php
                    }
                    ?>
                    <a href="./php/actions?a=deletePost&i=<?php echo $postData['postId']?>&n=<?php echo $postData['postTitle']?>">
                    <button name="deleteService" class="btn btn-danger btn-sm text-white">
                        Delete
                    </button>
                    </a>
                </div>
            </div>
        </div>
        <!-- <li>
            <a href="services?d=<?php echo $getService['serviceId'] ?>&n=<?php echo $getService['servicename'] ?>"><?php echo $getService['servicename'] ?></a>
        </li> -->
        <?php
    }
}
?>