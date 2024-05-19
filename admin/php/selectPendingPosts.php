<?php
$selectPendingposts = mysqli_query($conn, "SELECT * FROM posts WHERE projStatus=0 ORDER BY uploadDate DESC");
if ($selectPendingposts->num_rows > 0) {
    while ($pendingPostData = mysqli_fetch_assoc($selectPendingposts)) {
        ?>
        <div class="d-flex flex-row comment-row mt-0">
            <div class="p-2">
                <img width="100" src="./uploads/posts/<?php echo $pendingPostData['projectImg1'] ?>" />
            </div>
            <div class="comment-text w-100">
                <h6 class="font-medium">
                    <?php echo $pendingPostData['postTitle'] ?>
                </h6>
                <!-- <span class="mb-3 d-block">Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </span> -->
                <div class="comment-footer">
                    <span class="text-muted float-end">
                        <?php echo $pendingPostData['uploadDate'].' '.$pendingPostData['uploadTime'] ?>
                    </span>
                    <a
                    target="_blank" href="edit-post?edit=true&i=<?php echo $pendingPostData['postId'] ?>&n=<?php echo $pendingPostData['postTitle'] ?>">
                        <button type="button" class="btn btn-cyan btn-sm text-white">
                            Edit
                        </button>
                    </a>
                    <a
                        target="_blank" href="https://www.icyezainteriors.com/project-prev?edit=true&i=<?php echo $pendingPostData['postId'] ?>&n=<?php echo $pendingPostData['postTitle'] ?>">
                        <button type="button" class="btn btn-warning btn-sm text-white">
                            View
                        </button>
                    </a>
                    <?php
                    if ($pendingPostData['projStatus'] == 0) {
                        ?>
                        <a href="./php/actions?a=publishPost&i=<?php echo $pendingPostData['postId']?>&n=<?php echo $pendingPostData['postTitle']?>">
                        <button name="publishPost" class="btn btn-success btn-sm text-white">
                            Publish
                        </button>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="./php/actions?a=unPublishPost&i=<?php echo $pendingPostData['postId']?>&n=<?php echo $pendingPostData['postTitle']?>">
                        <button name="unPublishPost" class="btn btn-success btn-sm text-white">
                            Un Publish
                        </button>
                        </a>
                        <?php
                    }
                    ?>
                    <a href="./php/actions?a=deletePost&i=<?php echo $pendingPostData['postId']?>&n=<?php echo $pendingPostData['postTitle']?>">
                    <button name="deleteService" class="btn btn-danger btn-sm text-white">
                        Delete
                    </button>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <div class="d-flex flex-row comment-row mt-0">
        <div class="comment-text w-100">
            <h6 class="font-medium">
                No pending Post approvals
            </h6>
        </div>
    </div>
    <?php
}
?>