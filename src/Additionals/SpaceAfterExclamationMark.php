<?php

final class SpaceAfterExclamationMark extends AdditionalPass {
	public function candidate(string $source, array $foundTokens): bool {
		if (isset($foundTokens[ST_EXCLAMATION])) {
			return true;
		}
		return false;
	}

	public function format(string $source): string{
		$this->tkns = token_get_all($source);
		$this->code = '';

		while (list($index, $token) = each($this->tkns)) {
			list($id, $text) = $this->getToken($token);
			$this->ptr = $index;
			switch ($id) {
			case ST_EXCLAMATION:
				$this->appendCode(
					$text .
					$this->getSpace(!$this->rightUsefulTokenIs([
						T_BOOLEAN_AND, T_BOOLEAN_OR,
						T_LOGICAL_AND, T_LOGICAL_OR, T_LOGICAL_XOR,
					]))
				);
				break;
			default:
				$this->appendCode($text);
				break;
			}
		}

		return $this->code;
	}

	public function getDescription(): string {
		return 'Add space after exclamation mark.';
	}

	public function getExample(): string {
		echo '
<?php
// From:
if (!true) foo();

// To:
if (! true) foo();
';
	}
}