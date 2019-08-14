import pygame
import sys
from settings import *


class Program:
    def __init__(self):
        pygame.init()
        self.clock = pygame.time.Clock()
        self.screen = pygame.display.set_mode((SCREEN_W, SCREEN_W))
        self.running = True

    def run(self):
        while self.running:
            self.get_events()
            self.update()
            self.draw()
            self.clock.tick(FPS)
        pygame.quit()
        sys.exit

    def get_events(self):
        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                self.running = False
            if event.type == pygame.KEYDOWN and event.key == pygame.K_ESCAPE:
                self.running = False

    def update(self):
        pass

    def draw(self):
        self.screen.fill(BG_COL)
        pygame.display.update()