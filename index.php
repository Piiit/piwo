<?php
		$body = $module == null ? null : $module->getDialog();
		if($body == null) {
			$defaultModule->execute();
			$body = $defaultModule->getDialog();
		}
	} catch (Exception $e) {
			$body = $showPagesModule->getDialog();
	}