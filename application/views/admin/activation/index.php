<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo html_escape($title); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Application Activation</h3>
                    </div>
                    <div class="box-body text-center">
                        <p class="lead">Application Status</p>
                        <p><span class="label label-success" style="font-size:15px;">● Activated</span></p>
                        <?php if (!empty($status['activated_at'])) { ?>
                            <p class="text-muted">Activated on <?php echo html_escape($status['activated_at']); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
