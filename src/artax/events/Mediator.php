<?php

/**
 * Artax Mediator Class File
 * 
 * PHP version 5.4
 * 
 * @category   artax
 * @package    core
 * @subpackage events
 * @author     Daniel Lowrey <rdlowrey@gmail.com>
 */

namespace artax\events;

/**
 * Mediator Class
 * 
 * @category   artax
 * @package    core
 * @subpackage events
 * @author     Daniel Lowrey <rdlowrey@gmail.com>
 */
class Mediator implements MediatorInterface
{
    /**
     * An array of event listeners
     * @var array
     */
    protected $listeners = [];
    
    /**
     * An optional object to which Closure listeners should bind `$this` references
     * @var mixed
     */
    protected $rebindObj;
    
    /**
     * Connect a `$listener` to the end of the `$eventName` event queue
     * 
     * If the specified listener is an instance of Closure and the `$rebind`
     * parameter is set to TRUE (default), the method will rebind the listener
     * to the scope of the object referenced by the `Mediator::$rebindObj` (if the
     * property is initialized). Specifying the `$rebind` parameter as `FALSE`
     * allows Closure listeners to specify their own `$this` binding prior to 
     * being attached to the mediator and maintain said reference once attached.
     * 
     * @param string $eventName Event identifier name to listen for
     * @param mixed  $listener  Callable event listener
     * @param bool   $rebind    Closure rebinding flag
     * 
     * @return int Returns the new number of listeners in the queue for the
     *             specified event.
     *
     * @throws LogicException when $listener is not an array or Traversable, or 
     *         if it is not callable.
     */
    public function push($eventName, $listener, $rebind=TRUE)
    {

        if (is_array($listener) || $listener instanceof Traversable) {
            foreach ($listener as $listenerItem) {
                $this->push($eventName, $listener, $rebind);
            }
        } elseif (!is_callable($listener)) {
            throw new InvalidArgumentException(
                'Argument 2 for' . get_class($this)
                . '::push must be an array, Traversable, or callable'
            );
        }

        if ($rebind && $listener instanceof \Closure && $this->rebindObj) {
            $listener = $listener->bindTo($this->rebindObj);
        }
              
        if ( ! isset($this->listeners[$eventName])) {
            $this->listeners[$eventName]   = [];
            $this->listeners[$eventName][] = $listener;
            return 1;
        }
        
        return array_push($this->listeners[$eventName], $listener);
    }

    /**
     * Iterates through the items in the order they are traversed, adding them
     * to the event queue found in the key.
     *
     * @param array|Traversable|StdClass     The variable to loop through.
     * @param bool   $rebind    Closure rebinding flag

     * @return void
     *
     * @throws LogicException when $iterable is not an array, Traversable, or
     *         StdClass.
     */
    public function pushAll($iterable, $rebind = TRUE) {
        if (!(is_array($iterable) 
            || $iterable instanceof Traversable
            || $iterable instanceof StdClass) 
        {
            throw new InvalidArgumentException(
                'Argument passed to pushAll was not an array, Traversable, nor StdClass'
            );
        }

        foreach ($iterable as $event => $value) {
            $this->push($event, $value, $rebind);
        }

    }
    
    /**
     * Connect an event listener to the front of the `$eventName` event queue
     * 
     * The `Mediator::unshift` method utilizes the `$rebind` parameter in the
     * same way as `Mediator::push`.
     * 
     * @param string $eventName Event identifier name to listen for
     * @param mixed  $listener  Event listener
     * @param bool   $rebind    Closure rebinding flag
     * 
     * @return int Returns the new number of listeners in the queue for the
     *             specified event.
     */
    public function unshift($eventName, callable $listener, $rebind=TRUE)
    {
        if ($rebind && $listener instanceof \Closure && $this->rebindObj) {
            $listener = $listener->bindTo($this->rebindObj);
        }
        
        if ( ! isset($this->listeners[$eventName])) {
            $this->listeners[$eventName]   = [];
        }
        return array_unshift($this->listeners[$eventName], $listener);
    }
    
    /**
     * Remove the first listener from the front of the `$eventName` event queue
     * 
     * @param string $eventName Event identifier name to listen for
     * 
     * @return mixed Returns shifted listener on success or `NULL` if no listeners
     *               were found for the specified event.
     */
    public function shift($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            return array_shift($this->listeners[$eventName]);
        }
        return NULL;
    }
    
    /**
     * Remove the last listener from the end of the `$eventName` event queue
     * 
     * @param string $eventName Event identifier name to listen for
     * 
     * @return mixed Returns popped listener on success or `NULL` if no listeners
     *               were found for the specified event.
     */
    public function pop($eventName)
    {
        if (isset($this->listeners[$eventName])) {
            return array_pop($this->listeners[$eventName]);
        }
        return NULL;
    }
    
    /**
     * Clear all listeners from the specified event queue
     * 
     * Clears all listeners for the specified event. If an empty parameter value
     * is passed for the `$eventName`, all listeners will be cleared from all
     * events.
     * 
     * @param string $eventName Event identifier name
     * 
     * @return void
     */
    public function clear($eventName=NULL)
    {
        if ($eventName && isset($this->listeners[$eventName])) {
            unset($this->listeners[$eventName]);
        } else {
            $this->listeners = [];
        }
    }
    
    /**
     * Retrieve a count of all listeners in the queue for a specific event
     * 
     * @param string $eventName Event identifier name
     * 
     * @return int Returns a count of queued listeners for the specified event
     */
    public function count($eventName)
    {
        return isset($this->listeners[$eventName])
            ? count($this->listeners[$eventName])
            : 0;
    }
    
    /**
     * Retrieve a list of all listened-for events in the queue
     * 
     * @return array Returns an array of listened-for events in the queue
     */
    public function keys()
    {
        return array_keys($this->listeners);
    }
    
    /**
     * Retrieve a list of all listeners queued for the specified event
     * 
     * @param string $eventName The event for which listeners should be returned
     * 
     * @return array Returns an array of queued listeners for the specified event
     */
    public function all($eventName)
    {
        return $eventName && isset($this->listeners[$eventName])
            ? $this->listeners[$eventName]
            : NULL;
    }
    
    /**
     * Retrieve the first event listener in the queue for the specified event
     * 
     * @param string $eventName Event identifier name
     * 
     * @return callable Returns the first event listener in the queue or `NULL`
     *                  if none exist for the specified event.
     */
    public function first($eventName)
    {
        if (isset($this->listeners[$eventName][0])) {
            return $this->listeners[$eventName][0];
        }
        
        return NULL;
    }
    
    /**
     * Retrieve the last event listener in the queue for the specified event
     * 
     * @param string $eventName Event identifier name
     * 
     * @return callable Returns the last event listener in the queue or `NULL`
     *                  if none exist for the specified event.
     */
    public function last($eventName)
    {
        if (isset($this->listeners[$eventName])
            && $count = count($this->listeners[$eventName])) {
          
            return $this->listeners[$eventName][$count-1];
        }
        
        return NULL;
    }
    
    /**
     * Notify listeners that an event has occurred
     * 
     * Listeners are treated as a queue in which the first registered listener
     * executes first, continuing down the queue until a listener returns `FALSE`
     * or the end of the queue is reached.
     * 
     * @param string $eventName Event identifier name
     * @param string $data      Optional data to pass to listeners
     * 
     * @return int Returns a count of listeners invoked for the notified event
     */
    public function notify($eventName, $data=NULL)
    {
        $execCount = 0;
        
        if (isset($this->listeners[$eventName])) {
            foreach ($this->listeners[$eventName] as $callable) {
                ++$execCount;
                if ($callable($data) === FALSE) {
                    return $execCount;
                }
            }
        }
        
        return $execCount;
    }
    
    /**
     * Specify an object to which Closure listeners should be rebound
     * 
     * @param mixed $obj
     * 
     * @return Mediator Returns object instance for method chaining
     * @throws InvalidArgumentException If passed a non-object parameter
     */
    public function setRebindObj($obj)
    {
        if ( ! is_object($obj)) {
            $msg = 'Mediator::setRebindObj requires an object parameter: ' .
                gettype($obj) . ' specified';
            throw new \InvalidArgumentException($msg);
        }
        $this->rebindObj = $obj;
        return $this;
    }
}