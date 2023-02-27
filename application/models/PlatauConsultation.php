<?php

class Model_PlatauConsultation
{
    private $id;
    private $statutAvis;
    private $dateAvis;
    private $statutPec;
    private $datePec;

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setStatutAvis(string $statutAvis): self
    {
        $this->statutAvis = $statutAvis;

        return $this;
    }

    public function getStatutAvis(): string
    {
        return $this->statutAvis;
    }

    public function setDateAvis(string $dateAvis): self
    {
        $this->dateAvis = $dateAvis;

        return $this;
    }

    public function getDateAvis(): string
    {
        return $this->dateAvis;
    }

    public function setStatutPec(string $statutPec): self
    {
        $this->statutPec = $statutPec;

        return $this;
    }

    public function getStatutPec(): string
    {
        return $this->statutPec;
    }

    public function setDatePec(string $datePec): self
    {
        $this->datePec = $datePec;

        return $this;
    }

    public function getDatePec(): string
    {
        return $this->datePec;
    }
}
