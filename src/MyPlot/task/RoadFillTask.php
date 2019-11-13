<?php
declare(strict_types=1);
namespace MyPlot\task;

use MyPlot\MyPlot;
use MyPlot\Plot;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class RoadFillTask extends Task {
	/** @var MyPlot $plugin */
	private $plugin;
	private $roadCounts, $plots, $level, $height, $bottomBlock, $plotFillBlock, $plotFloorBlock, $plotBeginPos, $xMax, $zMax, $maxBlocksPerTick;

	/**
	 * PlotMergeTask constructor.
	 *
	 * @param MyPlot $plugin
	 * @param Plot[] $plots
	 * @param int $maxBlocksPerTick
	 */
	public function __construct(MyPlot $plugin, array $plots, int $maxBlocksPerTick = 256) {
		$this->plugin = $plugin;
		foreach($plots as $plot) {
			$key = $plot->__toString().$plot->levelName;
			$this->roadCounts[$key] = count($plugin->getProvider()->getMergedPlots($plot, true));
			$this->plots[$key] = $plot;
		}
		$this->maxBlocksPerTick = $maxBlocksPerTick;
		$this->pos = new Vector3($this->plotBeginPos->x, 0, $this->plotBeginPos->z);
	}

	public function onRun(int $currentTick) {
		$blocks = 0;
		while($this->pos->x < $this->xMax) {
			while($this->pos->z < $this->zMax) {
				if($this->plugin->getPlotByPosition(Position::fromObject($this->pos, $this->level)) === null) {
					while($this->pos->y < $this->level->getWorldHeight()) {
						if($this->pos->y === 0) {
							$block = $this->bottomBlock;
						}elseif($this->pos->y < $this->height) {
							$block = $this->plotFillBlock;
						}elseif($this->pos->y === $this->height) {
							$block = $this->plotFloorBlock;
						}else{
							$block = Block::get(Block::AIR);
						}
						$this->level->setBlock($this->pos, $block, false, false);
						$blocks++;
						if($blocks >= $this->maxBlocksPerTick) {
							$this->plugin->getScheduler()->scheduleDelayedTask($this, 1);
							return;
						}
						$this->pos->y++;
					}
					$this->pos->y = 0;
				}
				$this->pos->z++;
			}
			$this->pos->z = $this->plotBeginPos->z;
			$this->pos->x++;
		}
	}
}