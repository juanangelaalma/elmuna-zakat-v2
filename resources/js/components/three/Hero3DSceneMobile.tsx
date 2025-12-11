import { Float, MeshDistortMaterial, Sphere } from '@react-three/drei';
import { Canvas, useFrame } from '@react-three/fiber';
import { Suspense, useRef } from 'react';
import * as THREE from 'three';

function FloatingBlob({ position, color, scale = 1 }: { position: [number, number, number]; color: string; scale?: number }) {
    const meshRef = useRef<THREE.Mesh>(null);

    useFrame((state) => {
        if (meshRef.current) {
            meshRef.current.rotation.y = state.clock.elapsedTime * 0.2;
        }
    });

    return (
        <Float speed={1} rotationIntensity={0.2} floatIntensity={0.4} floatingRange={[-0.3, 0.3]}>
            <Sphere ref={meshRef} args={[1, 32, 32]} scale={scale} position={position}>
                <MeshDistortMaterial
                    color={color}
                    attach="material"
                    distort={0.3}
                    speed={1.5}
                    roughness={0.3}
                />
            </Sphere>
        </Float>
    );
}

function SceneMobile() {
    return (
        <>
            <ambientLight intensity={0.6} />
            <directionalLight position={[5, 5, 3]} intensity={0.8} />
            
            <FloatingBlob position={[0, 0, -1]} color="#93c5fd" scale={1} />
            <FloatingBlob position={[1.5, -0.5, -2]} color="#a78bfa" scale={0.6} />
        </>
    );
}

export default function Hero3DSceneMobile({ className = '' }: { className?: string }) {
    return (
        <div className={className}>
            <Canvas
                camera={{ position: [0, 0, 4], fov: 50 }}
                gl={{
                    antialias: false,
                    alpha: true,
                    powerPreference: 'low-power',
                }}
                dpr={1}
            >
                <Suspense fallback={null}>
                    <SceneMobile />
                </Suspense>
            </Canvas>
        </div>
    );
}
