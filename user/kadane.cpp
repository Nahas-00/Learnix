#include <iostream>
#include <algorithm> // for std::max

int kadane(int arr[], int n) {
  int current_max = 0;  // Initialize maximum sum ending at current position
  int global_max = -2147483648;  // Initialize global maximum sum. Use the smallest possible integer for the correct answer when all numbers are negative

  for (int i = 0; i < n; ++i) {
    current_max = std::max(arr[i], current_max + arr[i]); //Key Step.Decide whether to extend the previous subarray or start a new one
    global_max = std::max(global_max, current_max); // Update global maximum if needed
  }

  return global_max;
}