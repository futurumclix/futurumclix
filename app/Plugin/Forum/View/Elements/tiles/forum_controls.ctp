<?php if ($user) {
    $isMod = $this->Forum->isMod($forum['Forum']['id']); ?>

    <div class="action-buttons <?php echo isset($class) ? $class : ''; ?>">
        <?php if ($settings['enableForumSubscriptions']) {
            if (empty($subscription)) {
                echo $this->Html->link(__d('forum', 'Subscribe'), array('controller' => 'stations', 'action' => 'subscribe', $forum['Forum']['id']), array('class' => 'btn btn-primary subscription', 'onclick' => 'return Forum.subscribe(this);'));
            } else {
                echo $this->Html->link(__d('forum', 'Unsubscribe'), array('controller' => 'stations', 'action' => 'unsubscribe', $subscription['Subscription']['id']), array('class' => 'btn btn-primary', 'onclick' => 'return Forum.unsubscribe(this);'));
            }
        }

        if ($isMod) {
            echo $this->Html->link(__d('forum', 'Moderate'), array('controller' => 'stations', 'action' => 'moderate', $forum['Forum']['slug']), array('class' => 'btn btn-primary info'));
        }

        if ($forum['Forum']['status']) {
            if ($this->Forum->hasAccess('Forum.Topic', 'create', $forum['Forum']['accessPost']) || $isMod) {
                echo $this->Html->link(__d('forum', 'Create Topic'), array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug']), array('class' => 'btn btn-primary'));
            }

            if ($this->Forum->hasAccess('Forum.Poll', 'create', $forum['Forum']['accessPoll']) || $isMod) {
                echo $this->Html->link(__d('forum', 'Create Poll'), array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug'], 'poll'), array('class' => 'btn btn-primary'));
            }
        } else {
            echo $this->Html->link(__d('forum', 'Closed'), 'javascript:;', array('class' => 'btn btn-primary disabled'));
        } ?>
    </div>
<?php } ?>