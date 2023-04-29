<?php

namespace GreenWix\prismaFrame\settings;

final class PrismaFrameSettings {

  public bool $debug = false;

  /** @var string[] */
  public array $supportedApiVersions;

  /**
   * @param string[] $supportedApiVersions
   */
  public static function new(array $supportedApiVersions): self {
    return new PrismaFrameSettings($supportedApiVersions);
  }

  public function withDebug(bool $debug = true): self {
    $this->debug = $debug;
    return $this;
  }

  /**
   * @param string[] $supportedApiVersions
   */
  private function __construct(array $supportedApiVersions) {
    $this->supportedApiVersions = $supportedApiVersions;
  }

}