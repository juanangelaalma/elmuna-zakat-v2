import { Environment, Float, MeshDistortMaterial, Sphere } from '@react-three/drei';
import { Canvas, useFrame } from '@react-three/fiber';
import { Suspense, useRef } from 'react';
import * as THREE from 'three';

function FloatingBlob({ position, color, scale = 1, speed = 1 }: { position: [number, number, number]; color: string; scale?: number; speed?: number }) {
    const meshRef = useRef<THREE.Mesh>(null);

    useFrame((state) => {
        if (meshRef.current) {
            meshRef.current.rotation.x = Math.sin(state.clock.elapsedTime * speed * 0.3) * 0.2;
            meshRef.current.rotation.y = Math.cos(state.clock.elapsedTime * speed * 0.2) * 0.3;
        }
    });

    return (
        <Float
            speed={speed}
            rotationIntensity={0.4}
            floatIntensity={0.6}
            floatingRange={[-0.5, 0.5]}
        >
            <Sphere ref={meshRef} args={[1, 64, 64]} scale={scale} position={position}>
                <MeshDistortMaterial
                    color={color}
                    attach="material"
                    distort={0.4}
                    speed={2}
                    roughness={0.2}
                    metalness={0.1}
                />
            </Sphere>
        </Float>
    );
}

function GeometricShape({ position, color, shape = 'box' }: { position: [number, number, number]; color: string; shape?: 'box' | 'torus' | 'octahedron' }) {
    const meshRef = useRef<THREE.Mesh>(null);

    useFrame((state) => {
        if (meshRef.current) {
            meshRef.current.rotation.x += 0.01;
            meshRef.current.rotation.y += 0.01;
            meshRef.current.position.y = position[1] + Math.sin(state.clock.elapsedTime) * 0.3;
        }
    });

    return (
        <Float speed={1.5} rotationIntensity={0.5} floatIntensity={0.8}>
            <mesh ref={meshRef} position={position}>
                {shape === 'box' && <boxGeometry args={[1, 1, 1]} />}
                {shape === 'torus' && <torusGeometry args={[0.5, 0.2, 16, 32]} />}
                {shape === 'octahedron' && <octahedronGeometry args={[1, 0]} />}
                <meshStandardMaterial
                    color={color}
                    roughness={0.3}
                    metalness={0.8}
                    emissive={color}
                    emissiveIntensity={0.2}
                />
            </mesh>
        </Float>
    );
}

function Scene() {
    return (
        <>
            <ambientLight intensity={0.5} />
            <directionalLight position={[10, 10, 5]} intensity={1} />
            <pointLight position={[-10, -10, -5]} intensity={0.5} color="#a78bfa" />
            
            <FloatingBlob position={[-2, 0, -2]} color="#93c5fd" scale={1.2} speed={0.8} />
            <FloatingBlob position={[2, -1, -1]} color="#a78bfa" scale={0.8} speed={1.2} />
            <FloatingBlob position={[0, 1, -3]} color="#86efac" scale={1} speed={1} />
            
            <GeometricShape position={[-3, 1, -4]} color="#fbbf24" shape="octahedron" />
            <GeometricShape position={[3, -0.5, -3]} color="#f472b6" shape="torus" />
            <GeometricShape position={[0, -2, -2]} color="#60a5fa" shape="box" />
            
            <Environment preset="city" />
        </>
    );
}

export default function Hero3DScene({ className = '' }: { className?: string }) {
    return (
        <div className={className}>
            <Canvas
                camera={{ position: [0, 0, 5], fov: 50 }}
                gl={{
                    antialias: true,
                    alpha: true,
                    powerPreference: 'high-performance',
                }}
                dpr={[1, 2]}
            >
                <Suspense fallback={null}>
                    <Scene />
                </Suspense>
            </Canvas>
        </div>
    );
}
