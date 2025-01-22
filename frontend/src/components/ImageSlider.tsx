import React, { useEffect, useState } from "react";
import {
  Card,
  CardMedia,
  Typography,
  Container,
  Box,
  useMediaQuery,
  useTheme,
} from "@mui/material";

interface ImageSliderProps {
  servers: { name: string; imageUrl: string, id: number }[]; // Список серверов с изображениями
  initialSelectedIndex?: number; // Начальный выбранный индекс
  onServerSelect: (serverName: string) => void; // Callback для выбора сервера
}

const ImageSlider: React.FC<ImageSliderProps> = ({ servers, initialSelectedIndex = 0, onServerSelect }) => {
  // State to manage the selected image
  const [selectedImage, setSelectedImage] = useState<number | null>(initialSelectedIndex);
  // State to track hovered image
  const [hoveredImage, setHoveredImage] = useState<number | null>(null);

  // Handle image click
  const handleImageClick = (index: number) => {
    setSelectedImage(index);
    localStorage.setItem('server', JSON.stringify(servers[index]));
    onServerSelect(servers[index].name); // Вызываем callback с именем выбранного сервера
  };

  // Handle mouse enter
  const handleMouseEnter = (index: number) => {
    setHoveredImage(index);
  };

  // Handle mouse leave
  const handleMouseLeave = () => {
    setHoveredImage(null);
  };

  useEffect(() => {
    if (servers.length > 0 && initialSelectedIndex !== null) {
      setSelectedImage(initialSelectedIndex);
    }
  }, [initialSelectedIndex, servers]);

  // Check for mobile view
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down("sm"));

  return (
    <Container sx={{ paddingLeft: '0 !important', paddingRight: '0 !important', paddingBottom: '20px' }}>
      <Typography variant="h4" align="left" gutterBottom>
        Серверы
      </Typography>

      {/* Display all images in a single row without gaps */}
      <Box
        sx={{
          display: "flex",
          overflowX: "auto", // Enable horizontal scrolling if needed
          gap: 0, // No gap between images
          alignItems: "flex-end", // Align images to the bottom
          [theme.breakpoints.down("sm")]: {
            flexWrap: "wrap", // Wrap images on small screens
          }
        }}
      >
        {servers.map((server, index) => {
          // Determine if the image is active (selected or hovered)
          const isActive =
            selectedImage === index || (!isMobile && hoveredImage === index);

          return (
            <Card
              key={index}
              onClick={() => handleImageClick(index)}
              onMouseEnter={() => !isMobile && handleMouseEnter(index)}
              onMouseLeave={handleMouseLeave}
              sx={{
                flex: isActive ? "1 1 60%" : "0 1 20%", // Dynamic width
                border: "none", // Highlight active image
                boxShadow:
                  isActive
                    ? "0 0 10px rgba(0, 0, 0, 0.5)"
                    : "none", // Shadow for active image
                cursor: "pointer", // Show pointer on hover
                borderRadius: 0, // Remove rounded corners for seamless alignment
                transition: "flex 0.3s, border 0.3s, box-shadow 0.3s", // Smooth transition
                overflow: "hidden", // Ensure content doesn't overflow
                position: "relative", // For positioning the description
                background: "none",
                [theme.breakpoints.down("sm")]: {
                    flex: isActive ? "1 1 50%" : "0 1 50%",
                }
              }}
            >
              <CardMedia
                component="img"
                height="200" // Fixed height for all images
                image={server.imageUrl}
                alt={server.name}
                sx={{
                  display: "block", // Ensure no extra space below the image
                  width: "100%", // Ensure image fills the card
                  objectFit: "cover", // Maintain aspect ratio
                }}
              />
              {/* Description overlay */}
              <Box
                sx={{
                  position: "absolute",
                  bottom: 0,
                  left: 0,
                  right: 0,
                  backgroundColor: isActive
                    ? "#f5b759cf"
                    : "rgba(0, 0, 0, 0.5)", // Semi-transparent background
                  color: "white", // Text color
                  padding: "10px", // Padding for text
                }}
              >
                <Typography variant="h6" sx={{ fontSize: "1.2rem", fontWeight: "bold" }}>
                  {server.name}
                </Typography>
              </Box>
            </Card>
          );
        })}
      </Box>
    </Container>
  );
};

export default ImageSlider;