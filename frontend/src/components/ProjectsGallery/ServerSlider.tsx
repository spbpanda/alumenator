import React from 'react';
import ImageSlider from '../ImageSlider';

interface ServerSliderProps {
  servers: any[];
  initialSelectedIndex: number;
  onServerSelect: (serverName: string) => void;
}

const ServerSlider: React.FC<ServerSliderProps> = ({
  servers,
  initialSelectedIndex,
  onServerSelect,
}) => {
  return (
    <ImageSlider
      servers={servers}
      initialSelectedIndex={initialSelectedIndex}
      onServerSelect={onServerSelect}
    />
  );
};

export default ServerSlider;