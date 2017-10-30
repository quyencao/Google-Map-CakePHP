<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $this->fetch('title'); ?>
	</title>
    <style>
        .navbar {
            width: 100%;
        }

        .color {
            width: 25px;
            height: 25px;
        }
    </style>
	<?= $this->element('head'); ?>
    <?php
		echo $this->Html->meta('icon');

//		echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
                <a class="navbar-brand" href="/locations">Map</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item<?php echo $this->here == '/locations' ? ' active' : ''; ?>">
                            <a class="nav-link" href="/locations">Danh sách địa điểm <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item<?php echo $this->here == '/locations/add' ? ' active' : ''; ?>">
                            <a class="nav-link" href="/locations/add">Thêm địa điểm</a>
                        </li>
                    </ul>
                </div>
            </nav>
		</div>
		<div id="content">

			<?php echo $this->Flash->render(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
<!--		<div id="footer">-->
<!--			--><?php //echo $this->Html->link(
//					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
//					'https://cakephp.org/',
//					array('target' => '_blank', 'escape' => false, 'id' => 'cake-powered')
//				);
//			?>
<!--			<p>-->
<!--				--><?php //echo $cakeVersion; ?>
<!--			</p>-->
<!--		</div>-->
	</div>
<!--	--><?php //echo $this->element('sql_dump'); ?>
</body>
</html>
