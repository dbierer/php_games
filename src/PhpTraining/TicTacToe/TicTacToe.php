<?php
namespace PhpTraining\TicTacToe;
class TicTacToe
{
	const TTT_KEY  = 'tic_tac_toe';
	const MAX_ROWS = 3;
	const MAX_COLS = 3;
	const X        = 'X';
	const O        = 'O';
	const X_COLOR  = 'red';
	const O_COLOR  = 'blue';
	public $board  = [];

	// method defs
	public function __construct()
	{
		$this->board = $_SESSION[self::TTT_KEY] ?? $this->clearBoard();
	}
	public function __destruct()
	{
		$_SESSION[self::TTT_KEY] = $this->board;
	}
	public function clearBoard() : array
	{
		$this->board = [
			['', '', ''],
			['', '', ''],
			['', '', '']
		];
		return $this->board;
	}
	public function setPosition(string $current, int $row, int $col) : void
	{
		$this->board[$row][$col] = $current;
	}
	public function displayBoard() : string
	{
		$html = '';
		$html .= '<table width="80%">';
		$html .= '<tr><th class="cell_dims_hdr_row">&nbsp;</th>';
		for ($x = 1; $x <= self::MAX_COLS; $x++)
			$html .= '<th class="cell_dims_hdr_row">' . $x . '</th>';
		$html .= '</tr>';
		$y = 1;
		foreach ($this->board as $row) {
			$html .= '<tr>';
			$html .= '<th class="cell_dims_hdr_col">' . $y++ . '</th>';
			foreach ($row as $cell) {
				// determine color
				$class = 'x_o';
				if ($cell === self::X) $class .= ' x_color';
				if ($cell === self::O) $class .= ' o_color';
				$html .= '<td class="cell_border cell_dims_main"><span class="' . $class . '">' . $cell . '</span></td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		return $html;
	}
	public function isValidMove(int $row, int $col) : bool
	{
		$valid = TRUE;
		if ($row < 0 || $row >= self::MAX_ROWS) {
			$valid = FALSE;
		} elseif ($col < 0 || $col >= self::MAX_COLS) {
			$valid = FALSE;
		} elseif (!empty($this->board[$row][$col])) {
			$valid = FALSE;
		}
		return $valid;
	}

	/**
	 * Checks to see if there's a winner
	 * If winner, returns "X" or "O"
	 * If no winner, return ""
	 *
	 * @return string $win
	 */
	public function checkWin() : string
	{
		$win = '';
		if (empty($win = $this->checkWinCols())) {
			if (empty($win = $this->checkWinRows())) {
				$win = $this->checkWinDiagonal();
			}
		}
		return $win;
	}
	/**
	 * Checks to see if there's a winner by row
	 * If winner, returns "X" or "O"
	 * If no winner, return ""
	 *
	 * @return string $win
	 */
	public function checkWinRows() : string
	{
		$win = 0;
		for ($y = 0; $y < self::MAX_ROWS; $y++) {
			// store 1st character on the row
			$char = $this->board[0][$y];
			for ($x = 0; $x < self::MAX_COLS; $x++) {
				$win += (int) ($this->board[$x][$y] === $char);
			}
		}
		return ($win === self::MAX_ROWS) ? $char : '';
	}
	/**
	 * Checks to see if there's a winner by col
	 * If winner, returns "X" or "O"
	 * If no winner, return ""
	 *
	 * @return string $win
	 */
	public function checkWinCols() : string
	{
		$win = 0;
		for ($x = 0; $x < self::MAX_COLS; $x++) {
			// store 1st character on the column
			$char = $this->board[$x][0];
			for ($y = 0; $y < self::MAX_ROWS; $y++) {
				$win += (int) ($this->board[$x][$y] === $char);
			}
		}
		return ($win === self::MAX_COLS) ? $char : '';
	}
	/**
	 * Checks to see if there's a winner by diagonals
	 * If winner, returns "X" or "O"
	 * If no winner, return ""
	 *
	 * @return string $win
	 */
	public function checkWinDiagonal() : string
	{
			// store top left
		$win = 0;
		$y   = 0;
		$char = $this->board[0][0];
		for ($x = 0; $x < self::MAX_COLS; $x++) {
			$win += (int) ($this->board[$x][$y] === $char);
		}
		// if no winner, try other diagonal
		if ($win !== self::MAX_COLS) {
			$win = 0;
			$x   = self::MAX_COLS - 1;
			$char = $this->board[$x][0];
			for ($y = 0; $y < self::MAX_ROWS; $y++) {
				$win += (int) ($this->board[$x--][$y] === $char);
			}
		}
		return ($win === self::MAX_COLS || $win === self::MAX_ROWS) ? $char : '';
	}
	/**
	 * Checks to see if it's a tie
	 *
	 * @return bool
	 */
	public function checkTie() : bool
	{
		$count = 0;
		for ($x = 0; $x < self::MAX_COLS; $x++) {
			for ($y = 0; $y < self::MAX_ROWS; $y++) {
				$count += (int) (!empty($this->board[$x][$y]));
			}
		}
		return ($count === (self::MAX_ROWS * self::MAX_COLS));
	}
	/**
	 * Swaps "X" for "O"
	 *
	 * @return string
	 */
	public function swap(string $current) : string
	{
		return ($current === self::X) ? self::O : self::X;
	}
	/**
	 * Makes sure $current is "X" or "O"
	 *
	 * @return string
	 */
	public function sanitize(string $current) : string
	{
		return (strtoupper($current) === self::O) ? self::O : self::X;
	}
}
