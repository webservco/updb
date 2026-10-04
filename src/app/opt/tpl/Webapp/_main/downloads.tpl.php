<?php if (!empty($downloads['show'])) { ?>
<article class="downloads clearfix">
    <h4><?php echo __('Downloads', 'App'); ?></h4>

    <?php
    if (!empty($downloads['list'])) {
        foreach ($downloads['list'] as $item) { ?>
            <div class="download">
                <strong>
                    <a href="<?php echo $url['www']; ?>Pub/App/PubIssueDownload/issue/<?php echo $item['pub_issue_id']; ?>/download/<?php echo $item['download_id']; ?>" title="Download">
                        <?php echo $item['file_name']; ?>
                    </a>
                </strong>
            </div>
        <?php
        }
    } else {
        echo '<p>' . __('No downloads yet', 'App') . '</p>';
    }
    ?>
</article>
    <br>
<?php } ?>
