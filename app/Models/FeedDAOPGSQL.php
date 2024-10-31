<?php
declare(strict_types=1);

class FreshRSS_FeedDAOPGSQL extends FreshRSS_FeedDAO {

	#[\Override]
	public function updateCachedValues(int ...$feedIds): int|false {
		if (count($feedIds) > 0) {
			// 2 sub-requests with FOREIGN KEY(e.id_feed), INDEX(e.is_read) faster than 1 request with GROUP BY or CASE
			$sql = <<<'SQL'
				UPDATE `_feed`
				SET `cache_nbEntries`=(SELECT COUNT(e1.id) FROM `_entry` e1 WHERE e1.id_feed=`_feed`.id),
					`cache_nbUnreads`=(SELECT COUNT(e2.id) FROM `_entry` e2 WHERE e2.id_feed=`_feed`.id AND e2.is_read=0)
			SQL;
			$sql .= ' WHERE id IN (' . str_repeat('?,', count($feedIds) - 1) . '?)';
		} else {
			// GROUP BY approach is a bit slower for small databases but much faster for large databases
			$sql = <<<'SQL'
				UPDATE `_feed`
				SET `cache_nbEntries` = e.total_entries,
					`cache_nbUnreads` = e.unread_entries
				FROM (
					SELECT id_feed,
						COUNT(id) AS total_entries,
						SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread_entries
					FROM `_entry`
					GROUP BY id_feed
				) AS e;
			SQL;
		}
		$stm = $this->pdo->prepare($sql);
		if ($stm !== false && $stm->execute($feedIds)) {
			return $stm->rowCount();
		} else {
			$info = $stm === false ? $this->pdo->errorInfo() : $stm->errorInfo();
			Minz_Log::error('SQL error ' . __METHOD__ . json_encode($info));
			return false;
		}
	}
}
