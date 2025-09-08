<?php
/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(0); // Ensure no page numbers are displayed
?>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getPrevious() : '#' ?>" aria-label="<?= lang('Pager.previous') ?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php foreach ($pager->links() as $link): ?>
            <li <?= $link['active'] ? 'class="page-item active"' : 'class="page-item"' ?>>
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNext() ? $pager->getNext() : '#' ?>" aria-label="<?= lang('Pager.next') ?>">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>