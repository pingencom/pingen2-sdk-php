<?php

declare(strict_types=1);

namespace Pingen\Endpoints\DataTransferObjects\Letter;

use Pingen\Support\Input;

/**
 * Class LetterMetaDataAttributes
 * @package Pingen\DataTransferObjects\Letter
 *
 * @method LetterMetaDataAttributes setLetterMetaDataRecipient(LetterMetaDataRecipientAttributes $value)
 * @method LetterMetaDataAttributes setLetterMetaDataSender(LetterMetaDataSenderAttributes $value)
 */
class LetterMetaDataAttributes extends Input
{
    protected LetterMetaDataRecipientAttributes $letter_meta_data_recipient;

    protected LetterMetaDataSenderAttributes $letter_meta_data_sender;
}
