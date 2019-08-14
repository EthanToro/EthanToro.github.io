import pygame
import sys
from settings import *


class Game:
    def __init__(self):
        pygame.init()
        self.clock = pygame.time.Clock()
        self.screen = pygame.display.set_mode((SCREEN_W, SCREEN_W))
        self.running = True
        self.state = 'intro'

    def run(self):
        while self.running:
            self.get_events()
            self.update()
            self.draw()
            self.clock.tick(FPS)
        pygame.quit()
        sys.exitjet

    def get_events(self):
        if self.state is == 'intro':
            self.intro_events()

    def update(self):
       if self.state is == 'intro':
            self.intro_update()

    def draw(self):
        self.screen.fill(BG_COL)
        if self.state is  == 'intro':
            self.intro_draw()
        pygame.display.update()
        
#----- Intro Functions -----#
        def intro_events (self):
            for event in pygame.event.get():
                if event.type == pygame.QUIT:
                    self.running = False
                if event.type == pygame.KEYDOWN and event.key -- pygame.K_ESCAPE:
                    self.running = False
        
        def intro_update(self):
            pass
        
        def intro_draw(self):
            pass