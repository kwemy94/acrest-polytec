<?php

namespace App\Enums;

enum TypeRessource: string
{
    case Pdf = 'pdf';
    case Epub = 'epub';
    case Word = 'word';
    case Audio = 'audio';
    case Video = 'video';
    case Autre = 'autre';

    /** Extensions acceptées à l'envoi. */
    public const EXTENSIONS = ['pdf', 'epub', 'doc', 'docx', 'odt', 'rtf', 'mp3', 'wav', 'ogg', 'm4a', 'mp4', 'webm'];

    public static function depuisExtension(string $extension): self
    {
        return match (strtolower($extension)) {
            'pdf' => self::Pdf,
            'epub' => self::Epub,
            'doc', 'docx', 'odt', 'rtf' => self::Word,
            'mp3', 'wav', 'ogg', 'm4a' => self::Audio,
            'mp4', 'webm' => self::Video,
            default => self::Autre,
        };
    }

    public function libelle(): string
    {
        return match ($this) {
            self::Pdf => 'PDF',
            self::Epub => 'EPUB',
            self::Word => 'Document Word',
            self::Audio => 'Audio',
            self::Video => 'Vidéo',
            self::Autre => 'Fichier',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::Pdf => 'bi-file-earmark-pdf',
            self::Epub => 'bi-book',
            self::Word => 'bi-file-earmark-word',
            self::Audio => 'bi-file-earmark-music',
            self::Video => 'bi-file-earmark-play',
            self::Autre => 'bi-file-earmark',
        };
    }

    /** Le navigateur sait l'afficher sans téléchargement. */
    public function lisibleEnLigne(): bool
    {
        return in_array($this, [self::Pdf, self::Audio, self::Video], true);
    }
}
