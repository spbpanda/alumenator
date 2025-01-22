import React from 'react';
import { Box } from '@mui/material';
import FilterButton from './FilterButton';

interface FilterButtonsProps {
  selectedType: string;
  onTypeChange: (type: string) => void;
}

const FilterButtons: React.FC<FilterButtonsProps> = ({ selectedType, onTypeChange }) => {
  const filters = [
    { name: 'Все товары', type: 'all' },
    { name: 'Привилегии', type: 'group' },
    { name: 'Предметы', type: 'item' },
    { name: 'Валюта', type: 'currency' },
    { name: 'Другие', type: 'other' },
  ];

  return (
    <Box sx={{ display: 'flex', gap: 2, marginBottom: 3, flexWrap: 'wrap' }}>
      {filters.map((filter) => (
        <FilterButton
          key={filter.type}
          name={filter.name}
          click={() => onTypeChange(filter.type)}
          isActive={selectedType === filter.type}
        />
      ))}
    </Box>
  );
};

export default FilterButtons;