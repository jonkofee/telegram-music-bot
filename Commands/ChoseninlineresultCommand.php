<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Longman\TelegramBot\Commands\SystemCommands;
use Longman\TelegramBot\Commands\SystemCommand;
/**
 * Chosen inline result command
 *
 * Gets executed when an item from an inline query is selected.
 */
class ChoseninlineresultCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'choseninlineresult';
	/**
	 * @var string
	 */
	protected $description = 'Chosen result query';
	/**
	 * @var string
	 */
	protected $version = '1.1.1';
	/**
	 * Command execute method
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public function execute()
	{
		$inline_query = $this->getChosenInlineResult();
		$track_id        = $inline_query->getResultId();
		$inline_message_id = $inline_query->getInlineMessageId();

		(new \TrackInlinemessage([
			'track_id' => $track_id,
			'inline_message_id' => $inline_message_id
		]))->save();

		return parent::execute();
	}
}