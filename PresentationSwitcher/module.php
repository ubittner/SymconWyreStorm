<?php

/*
 * @module      WyreStorm PresentationSwitcher
 *
 * @prefix      UBWSPS
 *
 * @file        module.php
 *
 * @developer   Ulrich Bittner, a joint project of Normen Thiel and Ulrich Bittner
 * @copyright   (c) 2019
 * @license    	CC BY-NC-SA 4.0
 *              https://creativecommons.org/licenses/by-nc-sa/4.0/
 *
 * @version     1.00-1
 * @date        2019-01-03, 10:00
 * @lastchange  2019-01-03, 10:00
 *
 * @see         https://github.com/ubittner/SymconWyreStorm
 *
 * @guids       Library
 *              {DF93493B-5880-CC8B-4EF6-267D7DB9DCE2}
 *
 *              PresentationSwitcher
 *             	{805959F9-1FDA-BF94-30CB-D6044B30771A}
 *
 * @changelog   2019-01-03, 10:00, initial version 1.00-1
 *
 */

// Declare
declare(strict_types=1);

// Include
include_once __DIR__ . '/helper/UBWSPS_Autoload.php';

class WyreStormPresentationSwitcher extends IPSModule
{
    // Helper
    use UBWSPS_Commands;

    public function Create()
    {
        // Never delete this line!
        parent::Create();

        // Register properties
        $this->RegisterPropertyString('DeviceDesignation', 'Presentation Switcher');
        $this->RegisterPropertyString('DeviceModel', 'SW-0501-HDBT');
        $this->RegisterPropertyBoolean('LogReceiveData', false);
        $this->RegisterPropertyBoolean('EnablePowerSwitch', false);
        $this->RegisterPropertyBoolean('EnableSourceSelection', true);
        $this->RegisterPropertyString('SourceList', '');

        // Connect to client socket
        $this->ConnectParent('{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}');
    }

    public function ApplyChanges()
    {
        // Register messages

        // Wait until IP-Symcon is started
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        // Client socket
        $clientSocket = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
        if ($clientSocket > 0) {
            $this->RegisterMessage($clientSocket, IM_CHANGESTATUS);
        }

        // Never delete this line!
        parent::ApplyChanges();

        // Check kernel runlevel
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }

        // Rename instance
        $this->RenameInstance();

        // Register profiles
        $this->CreateSourcesProfile();

        // Register variables
        $this->RegisterVariableBoolean('Power', 'Power', '~Switch', 1);
        $this->EnableAction('Power');

        $profile = 'UBWSPS.' . $this->InstanceID . '.Sources';
        $this->RegisterVariableInteger('Sources', $this->Translate('Available sources'), $profile, 2);
        $this->EnableAction('Sources');
        $this->SetValue('Sources', 1);

        // Set the visibility of the switching modes
        $this->SetSwitchingModes();
    }

    public function Destroy()
    {
        // Never delete this line!
        parent::Destroy();

        // Delete profiles
        $profile = 'UBWSPS.' . $this->InstanceID . '.Sources';
        if (IPS_VariableProfileExists($profile)) {
            IPS_DeleteVariableProfile($profile);
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IPS_KERNELSTARTED:
                $this->KernelReady();
                break;
            case IM_CHANGESTATUS:
                $clientSocket = @IPS_GetInstance($this->InstanceID)['ConnectionID'];
                if ($SenderID == $clientSocket) {
                    if ($Data[0] == 102) {
                        // Login to device
                        $this->DeviceLogin();
                    }
                }
                break;
        }
    }

    /**
     * Apply changes when the kernel is ready.
     */
    protected function KernelReady()
    {
        $this->ApplyChanges();
    }

    /**
     * Sends the data to the client socket.
     *
     * @param string $data
     *
     * @return string
     */
    protected function SendData(string $data)
    {
        $result = null;
        $device = @IPS_GetInstance($this->InstanceID);
        $deviceState = $device['InstanceStatus'];
        if ($deviceState == 102) {
            $clientSocket = $device['ConnectionID'];
            if ($clientSocket > 0) {
                $clientSocketState = @IPS_GetInstance($clientSocket)['InstanceStatus'];
                if ($clientSocketState == 102) {
                    // Send data to client socket
                    $result = $this->SendDataToParent(json_encode(['DataID' => '{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}', 'Buffer' => $data]));
                }
            }
        }
        return $result;
    }

    /**
     * Receives data from the client socket.
     *
     * @param $JSONString
     *
     * @return bool|void
     */
    public function ReceiveData($JSONString)
    {
        $this->SendDebug('Receive:', $JSONString, 0);
        if ($this->ReadPropertyBoolean('LogReceiveData')) {
            $data = json_decode($JSONString);
            IPS_LogMessage('ReceiveData', utf8_decode($data->Buffer));
        }
    }

    /**
     * Renames this instance.
     */
    protected function RenameInstance()
    {
        $deviceDesignation = $this->ReadPropertyString('DeviceDesignation');
        if (!empty($deviceDesignation)) {
            IPS_SetName($this->InstanceID, $deviceDesignation);
        }
    }

    /**
     * Gets the switching modes from configuration form and enables / disables the visibility of the switches.
     */
    protected function SetSwitchingModes()
    {
        $modes = [];
        $modes[0]['name'] = 'Power';
        $modes[0]['status'] = 'EnablePowerSwitch';
        $modes[1]['name'] = 'Sources';
        $modes[1]['status'] = 'EnableSourceSelection';
        foreach ($modes as $mode) {
            $status = $this->ReadPropertyBoolean($mode['status']);
            $switchID = $this->GetIDForIdent($mode['name']);
            IPS_SetHidden($switchID, !$status);
        }
    }

    /**
     * Creates the standard profiles.
     */
    public function AddStandardSources()
    {
        $deviceModel = $this->ReadPropertyString('DeviceModel');
        switch ($deviceModel) {
            case 'SW-0501-HDBT':
            case 'NHD-SW-0501':
                $sourceNames = ['1' => 'HDMI1', '2' => 'HDMI2', '3' => 'HDMI3', '4' => 'HDMI4', '5' => 'VGA1'];
                break;
            case 'SW-1001-HDBT':
                $sourceNames = ['1' => 'HDMI1', '2' => 'HDMI2', '3' => 'HDMI3', '4' => 'HDMI4', '5' => 'HDMI5', '6' => 'HDMI6', '7' => 'DP', '8' => 'VGA1', '9' => 'VGA2', '10' => 'HDBT'];
                break;
            default:
                $sourceNames = null;
        }
        if (!is_null($sourceNames)) {
            $availableSources = [];
            foreach ($sourceNames as $key => $sourceName) {
                array_push($availableSources, ['Position' => $key, 'Source' => $sourceName, 'Description' => $sourceName]);
            }
            // Update available sources
            $json = json_encode($availableSources);
            IPS_SetProperty($this->InstanceID, 'SourceList', $json);
            if (IPS_HasChanges($this->InstanceID)) {
                IPS_ApplyChanges($this->InstanceID);
            }
        }
    }

    /**
     * Creates the profile for available audio sources.
     */
    protected function CreateSourcesProfile()
    {
        // Delete profile first
        $profile = 'UBWSPS.' . $this->InstanceID . '.Sources';
        if (IPS_VariableProfileExists($profile)) {
            IPS_DeleteVariableProfile($profile);
        }
        // Create profile
        if (!IPS_VariableProfileExists($profile)) {
            IPS_CreateVariableProfile($profile, 1);
        }
        IPS_SetVariableProfileIcon($profile, 'TV');
        IPS_SetVariableProfileValues($profile, 0, 0, 0);
        // Available sources
        $sources = json_decode($this->ReadPropertyString('SourceList'));
        if (!empty($sources)) {
            foreach ($sources as $source) {
                $position = $source->Position;
                if (empty($position)) {
                    $position = 0;
                }
                $description = $source->Description;
                if (empty($description)) {
                    $description = $source->Source;
                }
                // Create associations
                IPS_SetVariableProfileAssociation($profile, $position, $description, '', 0x0000ff);
            }
        } else {
            IPS_SetVariableProfileAssociation($profile, 1, 'None', '', 0x0000ff);
        }
    }

    //#################### Configuration form

    public function GetConfigurationForm()
    {
        $formData = json_decode(file_get_contents(__DIR__ . '/form.json'));
        $sources = json_decode($this->ReadPropertyString('SourceList'));
        if (!empty($sources)) {
            $status = true;
            foreach ($sources as $currentKey => $currentArray) {
                $rowColor = '';
                // Check for double entries
                foreach ($sources as $searchKey => $searchArray) {
                    // Check position
                    if ($searchArray->Position == $currentArray->Position) {
                        if ($searchKey != $currentKey) {
                            $rowColor = '#FFC0C0';
                            $status = false;
                        }
                    }
                    // Check source
                    if ($searchArray->Source == $currentArray->Source) {
                        if ($searchKey != $currentKey) {
                            $rowColor = '#FFC0C0';
                            $status = false;
                        }
                    }
                }
                // Check entries
                if (empty($currentArray->Position) || $currentArray->Source == 'Please select' || empty($currentArray->Description)) {
                    $rowColor = '#FFC0C0';
                    $status = false;
                }
                $formData->elements[2]->items[1]->values[] = ['Position' => $currentArray->Position, 'Source' => $currentArray->Source, 'Description' => $currentArray->Description, 'rowColor' => $rowColor];
                if ($status == false) {
                    $this->SetStatus(201);
                } else {
                    $this->SetStatus(102);
                }
            }
        }
        return json_encode($formData);
    }

    //#################### Request action

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            case 'Power':
                $this->TogglePower($Value);
                break;
            case 'Sources':
                $this->SelectSource($Value);
                break;
            default:
                $this->SendDebug('UBWSPS', 'Invalid ident', 0);
        }
    }

    //#################### Module functions

    /**
     * Toggles the power off / on.
     *
     * @param bool $State
     */
    public function TogglePower(bool $State)
    {
        $this->SetValue('Power', $State);
        if ($State) {
            $this->PowerOnDevice();
        } else {
            $this->PowerOffDevice();
        }
    }

    /**
     * Selects a source.
     *
     * @param int $Source
     */
    public function SelectSource(int $Source)
    {
        $this->SetValue('Sources', $Source);
        // Get available sources
        $availableSources = json_decode($this->ReadPropertyString('SourceList'));
        if (empty($availableSources)) {
            return;
        }
        // Get device source name
        $deviceSourceName = '';
        foreach ($availableSources as $availableSource) {
            if ($availableSource->Position == $Source) {
                $deviceSourceName = $availableSource->Source;
            }
        }
        // Send data to client socket
        if (!empty($deviceSourceName)) {
            $this->SelectDeviceSource($deviceSourceName);
        }
    }
}
