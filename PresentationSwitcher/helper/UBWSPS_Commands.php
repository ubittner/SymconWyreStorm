<?php

trait UBWSPS_Commands
{
    //#################### Control commands

    /**
     * Establishes the login to the device.
     */
    public function DeviceLogin()
    {
        $command = 'root';
        $data = $command . chr(13);
        $this->SendData($data);
    }

    /**
     * Reboots the device.
     */
    public function RebootDevice()
    {
        $command = 'reboot';
        $data = $command . chr(13);
        $this->SendData($data);
    }

    /**
     * Powers the device off.
     */
    public function PowerOffDevice()
    {
        // Sleep, CEC enabled device
        $command = 'e e_cec_system_standby';
        $data = $command . chr(13);
        $this->SendData($data);
    }

    /**
     * Powers the device on.
     */
    public function PowerOnDevice()
    {
        // Wake up, CEC enabled device
        $command = 'e e_cec_one_touch_play';
        $data = $command . chr(13);
        $this->SendData($data);
    }

    //#################### Source switching commands

    /**
     * Selects a dice source by name.
     *
     * @param string $SourceName
     */
    public function SelectDeviceSource(string $SourceName)
    {
        if (empty($SourceName)) {
            return;
        }
        $command = 'gbconfig --source-select ' . $SourceName;
        $data = $command . chr(13);
        $this->SendData($data);
    }

    /**
     * Queries the current device source.
     */
    public function QueryCurrentDeviceSource()
    {
        $command = 'gbconfig --show --source-select';
        $data = $command . chr(13);
        $this->SendData($data);
    }

    //#################### Audio commands

    /**
     * Decreases the volume level of AUDIO OUT port (incremental).
     */
    public function DecreaseDeviceVolumeIncremental()
    {
        $command = 'gbconfig --line-out=1 --level-down';
        $data = $command . chr(13);
        $this->SendData($data);
    }

    /**
     * Increases the volume level of AUDIO OUT port (incremental).
     */
    public function IncreaseDeviceVolumeIncremental()
    {
        $command = 'gbconfig --line-out=1 --level-up';
        $data = $command . chr(13);
        $this->SendData($data);
    }
}